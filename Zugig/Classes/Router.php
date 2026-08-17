<?php
class Router
{
    private array $routes = [];

    public function add(
        string $pattern,
        string $handler,
        array $constraints = []
    ): void {
        $regex = preg_replace_callback(
            '#:([\w]+)#',
            fn($m) => sprintf(
                '(%s)',
                $constraints[$m[1]] ?? '[^/]+'
            ),
            $pattern
        );

        preg_match_all('#:([\w]+)#', $pattern, $out);

        $this->routes[] = [
            'regex'    => '#^' . $regex . '$#',
            'handler'  => $handler,
            'params'   => $out[1],
        ];
    }

    public function dispatch(string $path, array $super = []): void
    {
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_combine($route['params'], array_slice($matches, 1));
                $this->call($route['handler'], $params + $super);
                return;
            }
        }

        $this->call($super['not_found_handler'] ?? 'App\Handler\NotFound::class', ['path' => $path] + $super);
    }

    private function call(string $handler, array $args): void
    {
        if (str_contains($handler, '::')) {
            [$class, $method] = explode('::', $handler);
            if (!method_exists($class, $method)) {
                throw new RuntimeException("{$class}::{$method} no existe");
            }
            $class::$method(...$args);
        } else {
            $obj = new $handler();
            $obj(...$args);
        }
    }
}

// Uso:
// $router = new Router();
// $router->add('/users/:id', App\Handler\UserById::class, ['id' => '\d+']);
// $router->add('/posts/:slug', App\Handler\PostBySlug::class);
// $router->dispatch($cleanPath, [
//     'not_found_handler' => App\Handler\NotFound::class,
// ]);
