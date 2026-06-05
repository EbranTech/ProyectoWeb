<?php
declare(strict_types=1);

// Define constants
define('BASE_PATH', dirname(__DIR__));

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = BASE_PATH . '/';

    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    // Mapping for core and app namespaces
    if (str_starts_with($class, 'Core\\')) {
        $file = BASE_PATH . '/core/' . substr($class, 5) . '.php';
    } elseif (str_starts_with($class, 'App\\')) {
        $file = BASE_PATH . '/app/' . substr($class, 4) . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once BASE_PATH . '/config/env.php';

use Core\Request;
use Core\Router;
use App\Middlewares\CorsMiddleware;

$request = new Request();

// Apply Global CORS Middleware
$cors = new CorsMiddleware();
$cors->handle($request);

$router = new Router();

// Load Routes
require_once BASE_PATH . '/routes/api.php';

$router->dispatch($request);
