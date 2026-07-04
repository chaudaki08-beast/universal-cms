<?php
namespace App\Core;

/**
 * Lightweight regex router supporting {param} placeholders, a generic
 * /admin/{controller}/{action}/{id} prefix dispatcher, and HTTP verbs.
 */
class Router
{
    /** @var array<int,array{method:string,pattern:string,handler:string}> */
    protected array $routes = [];

    /** @var array<int,array{prefix:string,namespace:string}> */
    protected array $prefixes = [];

    public function get(string $pattern, string $handler): void  { $this->add('GET', $pattern, $handler); }
    public function post(string $pattern, string $handler): void { $this->add('POST', $pattern, $handler); }
    public function any(string $pattern, string $handler): void  { $this->add('ANY', $pattern, $handler); }

    public function prefix(string $prefix, string $namespace): void
    {
        $this->prefixes[] = ['prefix' => rtrim($prefix, '/'), 'namespace' => $namespace];
    }

    protected function add(string $method, string $pattern, string $handler): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function dispatch(string $uri, string $method): void
    {
        $method = strtoupper($method);

        // 1. Exact / placeholder routes
        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }
            $regex = $this->toRegex($route['pattern']);
            if (preg_match($regex, $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->invoke($route['handler'], array_values($params));
                return;
            }
        }

        // 2. Prefix dispatcher: /admin/{controller}/{action}/{id}
        foreach ($this->prefixes as $p) {
            if ($uri === $p['prefix'] || strpos($uri, $p['prefix'] . '/') === 0) {
                $rest  = trim(substr($uri, strlen($p['prefix'])), '/');
                $parts = $rest === '' ? [] : explode('/', $rest);

                $controller = $parts[0] ?? 'Dashboard';
                $action     = $parts[1] ?? 'index';
                $id         = $parts[2] ?? null;

                $class = 'App\\Controllers\\' . $p['namespace'] . '\\'
                       . ucfirst($controller) . 'Controller';

                if (class_exists($class) && method_exists($class, $action)) {
                    $args = $id !== null ? [$id] : [];
                    (new $class())->{$action}(...$args);
                    return;
                }
            }
        }

        $this->notFound();
    }

    protected function toRegex(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    protected function invoke(string $handler, array $args): void
    {
        // Handler format: "Namespace\\NameController@action" (name already
        // includes the Controller suffix in registered routes).
        [$name, $action] = explode('@', $handler);
        $class = 'App\\Controllers\\' . $name;

        if (!class_exists($class) || !method_exists($class, $action)) {
            $this->notFound();
            return;
        }
        (new $class())->{$action}(...$args);
    }

    protected function notFound(): void
    {
        http_response_code(404);
        if (is_file(APP_PATH . '/Views/errors/404.php')) {
            require APP_PATH . '/Views/errors/404.php';
        } else {
            echo '404 Not Found';
        }
    }
}
