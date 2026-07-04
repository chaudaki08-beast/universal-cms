<?php
namespace App\Core;

/** Request input accessor with light sanitisation helpers. */
class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool { return self::method() === 'POST'; }

    /** Raw value (no escaping — escape at output time with e()). */
    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /** Trimmed string. */
    public static function str(string $key, string $default = ''): string
    {
        $v = self::input($key, $default);
        return is_string($v) ? trim($v) : $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        return (int) self::input($key, $default);
    }

    public static function float(string $key, float $default = 0.0): float
    {
        return (float) self::input($key, $default);
    }

    public static function bool(string $key): bool
    {
        $v = self::input($key);
        return in_array($v, ['1', 1, true, 'true', 'on', 'yes'], true);
    }

    public static function array(string $key): array
    {
        $v = self::input($key, []);
        return is_array($v) ? $v : [];
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
