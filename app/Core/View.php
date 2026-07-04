<?php
namespace App\Core;

/**
 * Simple PHP template renderer with layout support.
 * Views live in app/Views, use plain PHP, and escape with e().
 */
class View
{
    /** Render a view into a layout. */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        echo self::capture($view, $data, $layout);
    }

    public static function capture(string $view, array $data = [], ?string $layout = null): string
    {
        $content = self::partial($view, $data);

        if ($layout) {
            $data['content'] = $content;
            return self::partial("layouts/$layout", $data);
        }
        return $content;
    }

    /** Render a view fragment and return the string. */
    public static function partial(string $view, array $data = []): string
    {
        $file = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View not found: $view ($file)");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
