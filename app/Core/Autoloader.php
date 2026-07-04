<?php
namespace App\Core;

/**
 * PSR-4-ish autoloader. Maps the "App\" namespace to the /app directory.
 * No Composer required (cPanel friendly).
 */
class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class): void {
            $prefix  = 'App\\';
            $baseDir = APP_PATH . '/';

            if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';

            if (is_file($file)) {
                require $file;
            }
        });
    }
}
