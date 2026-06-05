<?php
declare(strict_types=1);

namespace Core;

class Router {
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }

    public function add(string $method, string $route, callable|array $handler, array $middlewares = []): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $this->compileRoute($route),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    private function compileRoute(string $route): string {
        // FIXED: Added closing delimiter '#' to the regex
        return '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route) . '$#';
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

                // FIXED: Handle Controller Class/Method arrays by instantiating the controller
                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controllerClass = $handler[0];
                    $method = $handler[1];
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $method], [$request, ...array_values($params)]);
                } else {
                    call_user_func_array($handler, [$request, ...array_values($params)]);
                }
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
