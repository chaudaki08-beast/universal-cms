<?php
namespace App\Core;

/** CSRF token generation + verification (synchronizer token pattern). */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** Hidden input for forms. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function check(?string $token): bool
    {
        return !empty($_SESSION['_csrf'])
            && is_string($token)
            && hash_equals($_SESSION['_csrf'], $token);
    }

    /** Verify on state-changing requests; aborts with 419 on failure. */
    public static function verify(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            if (!self::check($token)) {
                http_response_code(419);
                exit('Invalid or expired security token. Please refresh and try again.');
            }
        }
    }
}
