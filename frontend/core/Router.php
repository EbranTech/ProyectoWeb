<?php
declare(strict_types=1);

namespace Core;

class Router {
    private array $routes = [];

    public function add(string $action, callable $handler): void {
        $this->routes[$action] = $handler;
    }

    public function dispatch(string $action): void {
        if (isset($this->routes[$action])) {
            $this->routes[$action]();
        } else {
            $this->routes['index']();
        }
    }
}
