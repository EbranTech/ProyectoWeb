<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/config/env.php';
loadEnv(BASE_PATH . '/.env');

spl_autoload_register(function (string $className): void {
    $directories = [
        BASE_PATH . '/core/',
        BASE_PATH . '/app/Controllers/',
        BASE_PATH . '/app/Services/',
        BASE_PATH . '/app/Repositories/',
        BASE_PATH . '/app/Middlewares/',
        BASE_PATH . '/app/Validators/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$request = new Request();
$router = new Router();

$makeEmpleadoService = function (): EmpleadoService {
    $connection = Database::getConnection();
    $repository = new EmpleadoRepository($connection);

    return new EmpleadoService($repository);
};

$makeEmpleadoController = function () use ($makeEmpleadoService): EmpleadoController {
    static $empleadoController = null;

    if ($empleadoController instanceof EmpleadoController) {
        return $empleadoController;
    }

    $validator = new EmpleadoValidator();
    $empleadoController = new EmpleadoController($makeEmpleadoService, $validator);

    return $empleadoController;
};

$corsMiddleware = new CorsMiddleware();
$jsonMiddleware = new JsonMiddleware();
$authMiddleware = new AuthMiddleware();

$corsMiddleware->handle($request);
$jsonMiddleware->handle($request);

$protectedMiddlewares = [$authMiddleware];

require BASE_PATH . '/routes/api.php';

$router->dispatch($request);
