<?php

class Router
{
    private array $routes = [];
    private array $compiled = [];

    public function add(
        string $pattern,
        string $method = 'GET',
        string $handler = '',
        array $constraints = []
    ): self {
        $regex = preg_replace_callback(
            '#:([\w]+)#',
            fn($m) => sprintf('(?P<%s>%s)', $m[1], $constraints[$m[1]] ?? '[^/]+'),
            $pattern
        );

        $this->routes[] = [
            'pattern' => rtrim($regex, '/'),
            'method'  => strtoupper($method),
            'handler' => $handler,
        ];

        return $this;
    }

    public function dispatch(string $path = '', string $method = 'GET'): void
    {
        $path = rtrim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') continue;

            $regex = '#^' . $route['pattern'] . '$#';
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);
                $this->call($route['handler'], $params);
                return;
            }
        }

        $this->call('', ['path' => $path, 'method' => $method]);
    }

    private function call(string $handler, array $params): void
    {
        if ($handler === '' || $handler === '0') {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        if (str_contains($handler, '::')) {
            [$class, $method] = explode('::', $handler);
            if (!method_exists($class, $method)) {
                throw new RuntimeException("{$class}::{$method} no existe");
            }
            $class::$method(...$params);
        } elseif (str_contains($handler, '->')) {
            [$obj, $method] = explode('->', $handler);
            $obj->$method(...$params);
        } else {
            $obj = new $handler();
            if (method_exists($obj, 'handle')) {
                $obj->handle(...$params);
            } elseif (method_exists($obj, '__invoke')) {
                $obj(...$params);
            } else {
                throw new RuntimeException("{$handler} no tiene handle() ni __invoke()");
            }
        }
    }
}

// Uso:
// $router = new Router();
// $router->add('/', 'GET', App\Handler\Home::class);
// $router->add('/users/:id', 'GET', App\Handler\UserById::class, ['id' => '\d+']);
// $router->add('/posts/:slug', 'GET', App\Handler\PostBySlug::class);
// $router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
