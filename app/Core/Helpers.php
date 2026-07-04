<?php
/**
 * Global helper functions. This file lives in the GLOBAL namespace and is
 * required eagerly during bootstrap (see index.php), so it is never autoloaded.
 */

use App\Core\Session;
use App\Core\Csrf;

if (!function_exists('e')) {
    /** HTML-escape for safe output (XSS protection). */
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('base_url')) {
    /** Absolute URL to the app root (handles subfolder installs). */
    function base_url(string $path = ''): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return $scheme . '://' . $host . $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('uploads_url')) {
    function uploads_url(string $path): string
    {
        return base_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('admin_url')) {
    function admin_url(string $path = ''): string
    {
        return base_url('admin/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        $url = preg_match('#^https?://#', $path) ? $path : base_url($path);
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('old')) {
    /** Repopulate form fields after a validation failure. */
    function old(string $key, $default = '')
    {
        $old = Session::get('_old', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string { return Csrf::field(); }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-') ?: 'n-' . substr(md5($text . microtime()), 0, 6);
    }
}

if (!function_exists('setting')) {
    /** Read a global setting (cached). */
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('money')) {
    function money($amount): string
    {
        $symbol = setting('currency_symbol', '$');
        return $symbol . number_format((float) $amount, 2);
    }
}

if (!function_exists('json_field')) {
    /** Safely decode a JSON column to an array. */
    function json_field($value): array
    {
        if (is_array($value)) return $value;
        $d = json_decode((string) $value, true);
        return is_array($d) ? $d : [];
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        echo '<pre style="padding:16px;background:#111;color:#0f0;">';
        foreach ($vars as $v) { var_export($v); echo "\n"; }
        echo '</pre>';
        exit;
    }
}
