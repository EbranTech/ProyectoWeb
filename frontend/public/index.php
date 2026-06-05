<?php
declare(strict_types=1);

session_start();

// Define constants
define('BASE_PATH', dirname(__DIR__));

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = BASE_PATH . '/';

    if (str_starts_with($class, 'Core\\')) {
        require_once BASE_PATH . '/core/' . substr($class, 5) . '.php';
    } elseif (str_starts_with($class, 'Controllers\\')) {
        require_once BASE_PATH . '/controllers/' . substr($class, 12) . '.php';
    } elseif (str_starts_with($class, 'Models\\')) {
        require_once BASE_PATH . '/models/' . substr($class, 7) . '.php';
    }
});

require_once BASE_PATH . '/config/env.php';

use Core\Router;
use Core\ResponseView;

$router = new Router();

// Session handlers
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login handler - NOW FULLY INTEGRATED WITH BACKEND API
if (isset($_GET['action']) && $_GET['action'] === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $api = new \Models\UsuarioApiModel();
            // We use a generic request method because the Model doesn't have a 'login' method
            // In a more complex system, we'd add this to the Model.
            $client = new \Core\ApiClient();
            $result = $client->post('/auth/login', [
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? ''
            ]);

            // If we reach here, the API returned success: true
            $_SESSION['user_nombre'] = $result['nombre'];
            $_SESSION['user_username'] = $result['username'];
            $_SESSION['user_rol'] = $result['rol']; // 'ADMIN' or 'BIBLIOTECARIO'

            header('Location: index.php');
            exit;
        } catch (\RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }

    include BASE_PATH . '/views/login.php';
    exit;
}

// Auth Check
if (!isset($_SESSION['user_username'])) {
    header('Location: index.php?action=login');
    exit;
}

// Routes
$router->add('index', function() { (new \Controllers\PrestamoController())->index(); });
$router->add('usuarios', function() { (new \Controllers\UsuarioController())->index(); });
$router->add('usuarios_new', function() { (new \Controllers\UsuarioController())->create(); });
$router->add('usuarios_edit', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\UsuarioController())->edit($id);
});
$router->add('usuarios_update', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\UsuarioController())->update($id);
});
$router->add('usuarios_delete', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\UsuarioController())->delete($id);
});

$router->add('autores', function() { (new \Controllers\AutorController())->index(); });
$router->add('autores_new', function() { (new \Controllers\AutorController())->create(); });
$router->add('autores_edit', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\AutorController())->edit($id);
});
$router->add('autores_update', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\AutorController())->update($id);
});
$router->add('autores_delete', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\AutorController())->delete($id);
});

$router->add('estudiantes', function() { (new \Controllers\EstudianteController())->index(); });
$router->add('estudiantes_new', function() { (new \Controllers\EstudianteController())->create(); });
$router->add('estudiantes_edit', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\EstudianteController())->edit($id);
});
$router->add('estudiantes_update', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\EstudianteController())->update($id);
});
$router->add('estudiantes_delete', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\EstudianteController())->delete($id);
});

$router->add('libros', function() { (new \Controllers\LibroController())->index(); });
$router->add('libros_new', function() { (new \Controllers\LibroController())->create(); });
$router->add('libros_edit', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\LibroController())->edit($id);
});
$router->add('libros_update', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\LibroController())->update($id);
});
$router->add('libros_delete', function() {
    $id = (int)$_GET['id'];
    (new \Controllers\LibroController())->delete($id);
});

$router->add('consulta', function() { (new \Controllers\LibroController())->consulta(); });
$router->add('prestamos', function() { (new \Controllers\PrestamoController())->index(); });
$router->add('prestamos_new', function() { (new \Controllers\PrestamoController())->create(); });
$router->add('devoluciones', function() { (new \Controllers\PrestamoController())->devoluciones(); });
$router->add('devoluciones_update', function() { (new \Controllers\PrestamoController())->returnLoan(); });
$router->add('historial', function() { (new \Controllers\PrestamoController())->historial(); });

$router->dispatch($_GET['action'] ?? 'prestamos');
