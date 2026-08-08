<?php
/**
 * Key-value site settings with one-shot loading.
 */
class Settings
{
    private static ?array $cache = null;

    public static function load(): void
    {
        if (self::$cache !== null) {
            return;
        }
        self::$cache = [];
        try {
            foreach (DB::all('SELECT skey, svalue FROM ' . DB::table('settings')) as $row) {
                self::$cache[$row['skey']] = $row['svalue'];
            }
        } catch (Throwable $e) {
            self::$cache = [];
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        self::load();
        $v = self::$cache[$key] ?? '';
        return $v !== '' ? $v : $default;
    }

    public static function set(string $key, string $value): void
    {
        self::load();
        $exists = DB::val('SELECT COUNT(*) FROM ' . DB::table('settings') . ' WHERE skey = ?', [$key]);
        if ($exists) {
            DB::update('settings', ['svalue' => $value], 'skey = ?', [$key]);
        } else {
            DB::insert('settings', ['skey' => $key, 'svalue' => $value]);
        }
        self::$cache[$key] = $value;
    }
}

/** Convenience accessor. */
function setting(string $key, string $default = ''): string
{
    return Settings::get($key, $default);
}
