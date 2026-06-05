<?php
declare(strict_types=1);

namespace Core;

class Router {
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }

    public function add(string $method, string $route, callable $handler, array $middlewares = []): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $this->compileRoute($route),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    private function compileRoute(string $route): string {
        return '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route) . '$';
    }

    public function dispatch(Request $request): void {
        $method = $request->getMethod();
        $uri = $this->normalizeUri($request->getUri());

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['route'], $uri, $matches)) {
                $params = array_filter($matches, 'Ctype_alpha', ARRAY_FILTER_USE_KEY);

                // Execute Middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle($request);
                }

                // Execute Handler
                call_user_func_array($route['handler'], [$request, ...array_values($params)]);
                return;
            }
        }

        Response::error("Route not found", 404);
    }

    private function normalizeUri(string $uri): string {
        $uri = str_replace($this->basePath, '', $uri);
        return '/' . trim($uri, '/');
    }
}
