<?php
namespace App\Models;

use App\Core\Database;

/** Global key/value settings with in-request caching. */
class Setting extends Model
{
    protected static string $table = 'settings';
    protected static ?array $cache = null;

    protected static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all("SELECT `key`,`value` FROM settings") as $row) {
                self::$cache[$row['key']] = $row['value'];
            }
        }
        return self::$cache;
    }

    public static function get(string $key, $default = null)
    {
        $all = self::load();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, $value, string $group = 'general'): void
    {
        Database::run(
            "INSERT INTO settings (`group`,`key`,`value`) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `group` = VALUES(`group`)",
            [$group, $key, is_array($value) ? json_encode($value) : (string) $value]
        );
        if (self::$cache !== null) {
            self::$cache[$key] = $value;
        }
    }

    public static function group(string $group): array
    {
        $out = [];
        foreach (Database::all("SELECT `key`,`value` FROM settings WHERE `group` = ?", [$group]) as $r) {
            $out[$r['key']] = $r['value'];
        }
        return $out;
    }
}
