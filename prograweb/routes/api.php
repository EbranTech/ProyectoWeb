<?php

declare(strict_types=1);

$router->get('/api/empleados', function (Request $request) use ($makeEmpleadoController): void {
    $makeEmpleadoController()->index($request);
}, $protectedMiddlewares);

$router->get('/api/empleados/{id}', function (Request $request) use ($makeEmpleadoController): void {
    $makeEmpleadoController()->show($request);
}, $protectedMiddlewares);

$router->post('/api/empleados', function (Request $request) use ($makeEmpleadoController): void {
    $makeEmpleadoController()->store($request);
}, $protectedMiddlewares);

$router->put('/api/empleados/{id}', function (Request $request) use ($makeEmpleadoController): void {
    $makeEmpleadoController()->update($request);
}, $protectedMiddlewares);

$router->delete('/api/empleados/{id}', function (Request $request) use ($makeEmpleadoController): void {
    $makeEmpleadoController()->destroy($request);
}, $protectedMiddlewares);
