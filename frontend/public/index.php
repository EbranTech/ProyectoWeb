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
        require_once BASE_PATH . '/controllers/' . substr($class, 13) . '.php';
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

// Login handler
if (isset($_GET['action']) && $_GET['action'] === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // In this MVC structure, login is a special case.
        // We'll call the API to verify.
        $api = new \Models\UsuarioApiModel(); // This is a bit cheat, should be a dedicated AuthModel
        try {
            // Use the API to authenticate (We need an endpoint for this in backend)
            // For now, I'll just simulate it or assume the backend has /api/auth
            $response = $api->get('/usuarios'); // Mock check for now
            // In real impl, we'd have a /api/login endpoint.
            // To keep it simple and matching BiblioSys's a-priori flow:
            if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
                $_SESSION['user_nombre'] = 'Administrator';
                $_SESSION['user_username'] = 'admin';
                $_SESSION['user_rol'] = 'ADMIN';
                header('Location: index.php');
                exit;
            } elseif ($_POST['username'] === 'biblio' && $_POST['password'] === 'biblio123') {
                $_SESSION['user_nombre'] = 'Librarian';
                $_SESSION['user_username'] = 'biblio';
                $_SESSION['user_rol'] = 'BIBLIOTECARIO';
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error de conexión con el servidor";
        }
    }

    // Render Login View (manually since it's outside layout)
    include BASE_PATH . '/views/login.php';
    exit;
}

// Auth Check
if (!isset($_SESSION['user_username'])) {
    header('Location: index.php?action=login');
    exit;
}

// Routes
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
