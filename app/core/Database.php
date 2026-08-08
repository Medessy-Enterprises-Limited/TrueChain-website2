<?php
/**
 * Thin PDO wrapper with table prefixing.
 * Supports MySQL (production / GoDaddy) and SQLite (local testing).
 */
class DB
{
    private static ?PDO $pdo = null;
    private static string $prefix = '';
    private static string $driver = 'mysql';

    public static function init(array $cfg): void
    {
        self::$prefix = $cfg['prefix'] ?? '';
        self::$driver = $cfg['driver'] ?? 'mysql';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        if (self::$driver === 'sqlite') {
            $path = $cfg['sqlite_path'] ?? (APP_PATH . '/storage/tcic.sqlite');
            self::$pdo = new PDO('sqlite:' . $path, null, null, $options);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            self::$pdo->exec('PRAGMA journal_mode = WAL');
        } else {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $cfg['host'] ?? 'localhost',
                $cfg['port'] ?? '3306',
                $cfg['name'] ?? ''
            );
            self::$pdo = new PDO($dsn, $cfg['user'] ?? '', $cfg['pass'] ?? '', $options);
        }
    }

    public static function pdo(): PDO
    {
        return self::$pdo;
    }

    public static function driver(): string
    {
        return self::$driver;
    }

    /** Prefixed physical table name. */
    public static function table(string $name): string
    {
        return self::$prefix . $name;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** First row or null. */
    public static function get(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** All rows. */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** Single scalar value. */
    public static function val(string $sql, array $params = [])
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql = 'INSERT INTO ' . self::table($table)
            . ' (' . implode(', ', $cols) . ') VALUES ('
            . implode(', ', array_fill(0, count($cols), '?')) . ')';
        self::run($sql, array_values($data));
        return (int)self::$pdo->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = implode(', ', array_map(fn($c) => $c . ' = ?', array_keys($data)));
        $sql = 'UPDATE ' . self::table($table) . ' SET ' . $sets . ' WHERE ' . $where;
        return self::run($sql, array_merge(array_values($data), $whereParams))->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        return self::run('DELETE FROM ' . self::table($table) . ' WHERE ' . $where, $params)->rowCount();
    }
}
