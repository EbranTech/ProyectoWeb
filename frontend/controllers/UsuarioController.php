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

    public function index(): void {
        try {
            $usuarios = $this->model->getAll();
            ResponseView::layout('usuarios/listar', ['usuarios' => $usuarios], 'Usuarios');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('usuarios/listar', [], 'Usuarios');
        }
    }

    public function create(): void {
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
