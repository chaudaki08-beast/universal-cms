<?php
/**
 * Universal CMS - Front Controller
 * Single entry point. All requests (except real files) are routed here.
 */

declare(strict_types=1);

define('CMS_START', microtime(true));
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH);
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// ---------------------------------------------------------------------------
// 1. Not installed yet? Send the visitor to the installation wizard.
// ---------------------------------------------------------------------------
if (!file_exists(CONFIG_PATH . '/config.php')) {
    if (is_dir(BASE_PATH . '/install')) {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/install/');
        exit;
    }
    http_response_code(503);
    exit('CMS is not installed and the installer is missing.');
}

// ---------------------------------------------------------------------------
// 2. Bootstrap
// ---------------------------------------------------------------------------
require CONFIG_PATH . '/config.php';
require APP_PATH . '/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\App;

Autoloader::register();

// Global helper functions must be available everywhere.
require APP_PATH . '/Core/Helpers.php';

// Error reporting based on environment
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

date_default_timezone_set(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'UTC');

// ---------------------------------------------------------------------------
// 3. Dispatch
// ---------------------------------------------------------------------------
try {
    (new App())->run();
} catch (\Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        http_response_code(500);
        echo '<pre style="padding:20px;font:14px/1.5 monospace;color:#b00">';
        echo 'Error: ' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        if (is_file(APP_PATH . '/Views/errors/500.php')) {
            require APP_PATH . '/Views/errors/500.php';
        } else {
            echo 'Internal Server Error';
        }
        error_log('[CMS] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    }
}
