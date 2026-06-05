<?php
declare(strict_types=1);

use Core\Router;
use App\Controllers\UsuarioController;
use App\Controllers\AutorController;
use App\Controllers\EstudianteController;
use App\Controllers\LibroController;
use App\Controllers\PrestamoController;
use App\Controllers\AuthController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\RoleMiddleware;

/** @var Router $router */

// All routes use JsonMiddleware
$commonMiddlewares = [JsonMiddleware::class];
$protectedMiddlewares = [...$commonMiddlewares, AuthMiddleware::class];

// PUBLIC ROUTES
$router->add('POST', '/api/auth/login', [AuthController::class, 'login'], $commonMiddlewares);

// USUARIOS - ONLY ADMIN
$adminMiddlewares = [...$protectedMiddlewares, new RoleMiddleware(['ADMIN'])];
$router->add('GET', '/api/usuarios', [UsuarioController::class, 'index'], $adminMiddlewares);
$router->add('GET', '/api/usuarios/{id}', [UsuarioController::class, 'show'], $adminMiddlewares);
$router->add('POST', '/api/usuarios', [UsuarioController::class, 'create'], $adminMiddlewares);
$router->add('PUT', '/api/usuarios/{id}', [UsuarioController::class, 'update'], $adminMiddlewares);
$router->add('DELETE', '/api/usuarios/{id}', [UsuarioController::class, 'delete'], $adminMiddlewares);

// Autores
$router->add('GET', '/api/autores', [AutorController::class, 'index'], $protectedMiddlewares);
$router->add('GET', '/api/autores/{id}', [AutorController::class, 'show'], $protectedMiddlewares);
$router->add('POST', '/api/autores', [AutorController::class, 'create'], $protectedMiddlewares);
$router->add('PUT', '/api/autores/{id}', [AutorController::class, 'update'], $protectedMiddlewares);
$router->add('DELETE', '/api/autores/{id}', [AutorController::class, 'delete'], $protectedMiddlewares);

// Estudiantes
$router->add('GET', '/api/estudiantes', [EstudianteController::class, 'index'], $protectedMiddlewares);
$router->add('GET', '/api/estudiantes/lookup', [EstudianteController::class, 'lookupByCarnet'], $protectedMiddlewares);
$router->add('GET', '/api/estudiantes/{id}', [EstudianteController::class, 'show'], $protectedMiddlewares);
$router->add('POST', '/api/estudiantes', [EstudianteController::class, 'create'], $protectedMiddlewares);
$router->add('PUT', '/api/estudiantes/{id}', [EstudianteController::class, 'update'], $protectedMiddlewares);
$router->add('DELETE', '/api/estudiantes/{id}', [EstudianteController::class, 'delete'], $protectedMiddlewares);

// Libros
$router->add('GET', '/api/libros', [LibroController::class, 'index'], $protectedMiddlewares);
$router->add('GET', '/api/libros/lookup', [LibroController::class, 'lookupByIsbn'], $protectedMiddlewares);
$router->add('GET', '/api/libros/{id}', [LibroController::class, 'show'], $protectedMiddlewares);
$router->add('POST', '/api/libros', [LibroController::class, 'create'], $protectedMiddlewares);
$router->add('PUT', '/api/libros/{id}', [LibroController::class, 'update'], $protectedMiddlewares);
$router->add('DELETE', '/api/libros/{id}', [LibroController::class, 'delete'], $protectedMiddlewares);

// Prestamos
$router->add('GET', '/api/prestamos', [PrestamoController::class, 'index'], $protectedMiddlewares);
$router->add('POST', '/api/prestamos', [PrestamoController::class, 'create'], $protectedMiddlewares);
$router->add('POST', '/api/prestamos/return', [PrestamoController::class, 'returnLoan'], $protectedMiddlewares);
