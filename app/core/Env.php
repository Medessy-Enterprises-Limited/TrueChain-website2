<?php
/**
 * Environment-driven configuration for container platforms (Railway, Render, Fly).
 *
 * Shared hosts such as GoDaddy keep using app/config.php, written once by
 * install.php. Container platforms rebuild the filesystem on every deploy, so a
 * config file written at install time disappears the next time the app ships.
 * There, configuration comes from environment variables instead and the
 * database is provisioned automatically on first boot.
 */
class Env
{
    /** Read an environment variable, treating "" as absent. */
    public static function value(string $name, ?string $default = null): ?string
    {
        $v = getenv($name);
        if ($v === false || $v === '') {
            $v = $_ENV[$name] ?? $_SERVER[$name] ?? null;
        }
        return ($v === null || $v === '') ? $default : (string)$v;
    }

    public static function flag(string $name, bool $default = false): bool
    {
        $v = self::value($name);
        return $v === null ? $default : in_array(strtolower($v), ['1', 'true', 'on', 'yes'], true);
    }

    /** Connection URL as supplied by Railway/Heroku-style plugins. */
    private static function url(): ?string
    {
        foreach (['DATABASE_URL', 'MYSQL_URL', 'MYSQL_PUBLIC_URL'] as $name) {
            $v = self::value($name);
            if ($v !== null) {
                return $v;
            }
        }
        return null;
    }

    /** True when the environment alone can configure the app - no config.php needed. */
    public static function active(): bool
    {
        return self::url() !== null
            || self::value('MYSQLHOST') !== null
            || self::value('DB_HOST') !== null
            || strtolower((string)self::value('DB_DRIVER', '')) === 'sqlite';
    }

    /** @return array{driver:string,host:string,port:string,name:string,user:string,pass:string,prefix:string,sqlite_path:string} */
    public static function database(): array
    {
        $prefix = self::value('DB_PREFIX', 'tcic_');
        $sqlitePath = self::value('SQLITE_PATH', APP_PATH . '/storage/tcic.sqlite');

        $db = [
            'driver' => 'mysql', 'host' => 'localhost', 'port' => '3306',
            'name'   => '', 'user' => '', 'pass' => '',
            'prefix' => $prefix, 'sqlite_path' => $sqlitePath,
        ];

        if (strtolower((string)self::value('DB_DRIVER', '')) === 'sqlite') {
            $db['driver'] = 'sqlite';
            $dir = dirname($sqlitePath);
            if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new RuntimeException('Cannot create the SQLite directory at ' . $dir . '. Mount a writable volume there.');
            }
            return $db;
        }

        if (($url = self::url()) !== null) {
            $p = parse_url($url);
            if ($p === false || empty($p['host'])) {
                throw new RuntimeException('The database URL could not be parsed. Expected mysql://user:password@host:port/database.');
            }
            $scheme = strtolower($p['scheme'] ?? 'mysql');
            if (!in_array($scheme, ['mysql', 'mysql2', 'mariadb'], true)) {
                throw new RuntimeException(
                    'Unsupported database type "' . $scheme . '". This site supports MySQL and SQLite. '
                    . 'On Railway, add a MySQL database rather than PostgreSQL.'
                );
            }
            $db['host'] = $p['host'];
            $db['port'] = (string)($p['port'] ?? 3306);
            $db['name'] = ltrim($p['path'] ?? '', '/');
            $db['user'] = isset($p['user']) ? rawurldecode($p['user']) : '';
            $db['pass'] = isset($p['pass']) ? rawurldecode($p['pass']) : '';
            return $db;
        }

        // Railway's MySQL plugin also exports the parts individually.
        $db['host'] = (string)self::value('MYSQLHOST', self::value('DB_HOST', 'localhost'));
        $db['port'] = (string)self::value('MYSQLPORT', self::value('DB_PORT', '3306'));
        $db['name'] = (string)self::value('MYSQLDATABASE', self::value('DB_NAME', ''));
        $db['user'] = (string)self::value('MYSQLUSER', self::value('DB_USER', ''));
        $db['pass'] = (string)self::value('MYSQLPASSWORD', self::value('DB_PASS', ''));

        if ($db['name'] === '' || $db['user'] === '') {
            throw new RuntimeException('Incomplete database settings: MYSQLDATABASE and MYSQLUSER (or DB_NAME and DB_USER) are required.');
        }
        return $db;
    }

    /** Full config array in the same shape as app/config.php. */
    public static function config(): array
    {
        $db = self::database();

        // The key binds admin sessions to a browser. It must stay stable across
        // deploys or everyone is signed out, so derive a fallback from the
        // database credentials when APP_KEY is not set explicitly.
        $key = self::value('APP_KEY')
            ?? hash('sha256', 'tcic|' . $db['host'] . '|' . $db['name'] . '|' . $db['user'] . '|' . $db['pass']);

        return [
            'db'  => $db,
            'app' => [
                'key'          => $key,
                'session_name' => (string)self::value('SESSION_NAME', 'TCICSESS'),
                'session_idle' => (int)self::value('SESSION_IDLE', '3600'),
                'debug'        => self::flag('APP_DEBUG'),
            ],
        ];
    }

    /**
     * Create the schema and seed content on first boot. Safe to call repeatedly:
     * tables use CREATE TABLE IF NOT EXISTS and seeding only runs when no
     * administrator exists yet.
     */
    public static function provision(array $cfg): void
    {
        $adminEmail = self::value('ADMIN_EMAIL');
        $adminPass  = self::value('ADMIN_PASSWORD');
        $adminName  = (string)self::value('ADMIN_NAME', 'Administrator');

        if ($adminEmail === null || $adminPass === null) {
            throw new RuntimeException(
                'First-run setup needs an administrator account. Set the ADMIN_EMAIL and '
                . 'ADMIN_PASSWORD environment variables (ADMIN_NAME is optional), then redeploy.'
            );
        }
        if (strlen($adminPass) < 10) {
            throw new RuntimeException('ADMIN_PASSWORD must be at least 10 characters.');
        }

        require_once APP_PATH . '/core/Schema.php';
        require_once APP_PATH . '/core/Seed.php';

        // Two containers booting together must not both seed the content.
        $lock = @fopen(sys_get_temp_dir() . '/tcic-provision.lock', 'c');
        if ($lock !== false) {
            flock($lock, LOCK_EX);
        }

        try {
            foreach (Schema::statements($cfg['db']['driver'], $cfg['db']['prefix']) as $sql) {
                try {
                    DB::pdo()->exec($sql);
                } catch (PDOException $e) {
                    // Re-running is normal; ignore "already exists" collisions only.
                    if (stripos($e->getMessage(), 'exist') === false && stripos($e->getMessage(), 'Duplicate') === false) {
                        throw $e;
                    }
                }
            }

            if ((int)DB::val('SELECT COUNT(*) FROM ' . DB::table('users')) === 0) {
                Seed::run($adminName, $adminEmail, $adminPass);
            }
        } finally {
            if ($lock !== false) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }
}
