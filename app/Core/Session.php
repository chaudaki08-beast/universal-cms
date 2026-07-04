<?php
namespace App\Core;

/** Secure session wrapper + flash messages. */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('CMSSESSID');
        session_start();

        // Mitigate fixation: rotate id once per session.
        if (empty($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
        }
    }

    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function has(string $key): bool { return isset($_SESSION[$key]); }
    public static function forget(string $key): void { unset($_SESSION[$key]); }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ---- Flash messages ----
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    public static function getFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }
}
