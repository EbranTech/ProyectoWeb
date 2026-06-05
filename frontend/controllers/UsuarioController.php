<?php
declare(strict_types=1);

namespace Controllers;

use Models\UsuarioApiModel;
use Core\ResponseView;
use RuntimeException;

class UsuarioController {
    private UsuarioApiModel $model;

    public function __construct() {
        $this->model = new UsuarioApiModel();
    }

    private function ensureAdmin(): void {
        if (($_SESSION['user_rol'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'No autorizado para gestionar usuarios.';
            header('Location: index.php?action=prestamos');
            exit;
        }
    }

    public function index(): void {
        $this->ensureAdmin();

        try {
            $usuarios = $this->model->getAll();
            ResponseView::layout('usuarios/listar', ['usuarios' => $usuarios], 'Usuarios');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('usuarios/listar', ['usuarios' => []], 'Usuarios');
        }
    }

    public function create(): void {
        $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create($_POST);
                header('Location: index.php?action=usuarios');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        ResponseView::layout('usuarios/new', [], 'Nuevo Usuario');
    }

    public function edit(int $id): void {
        $this->ensureAdmin();

        try {
            $usuario = $this->model->getById($id);
            ResponseView::layout('usuarios/edit', ['usuario' => $usuario], 'Editar Usuario');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=usuarios');
            exit;
        }
    }

    public function update(int $id): void {
        $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->update($id, $_POST);
                header('Location: index.php?action=usuarios');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        $this->edit($id);
    }

    public function delete(int $id): void {
        $this->ensureAdmin();

        try {
            $this->model->delete($id);
            header('Location: index.php?action=usuarios');
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=usuarios');
            exit;
        }
    }
}
