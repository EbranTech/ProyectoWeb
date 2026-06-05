<?php
declare(strict_types=1);

namespace Controllers;

use Models\AutorApiModel;
use Core\ResponseView;
use RuntimeException;

class AutorController {
    private AutorApiModel $model;

    public function __construct() {
        $this->model = new AutorApiModel();
    }

    public function index(): void {
        try {
            $this->renderList();
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('autores/listar', ['autores' => []], 'Autores');
        }
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create($_POST);
                header('Location: index.php?action=autores');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->renderList(['formData' => $_POST]);
                return;
            }
        }
        $this->renderList();
    }

    public function edit(int $id): void {
        try {
            $autor = $this->model->getById($id);
            $this->renderList(['autor' => $autor], 'Editar Autor');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=autores');
            exit;
        }
    }

    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->update($id, $_POST);
                header('Location: index.php?action=autores');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->renderList([
                    'autor' => ['id_autor' => $id] + $_POST,
                    'formData' => $_POST,
                ], 'Editar Autor');
                return;
            }
        }
        $this->edit($id);
    }

    public function delete(int $id): void {
        try {
            $this->model->delete($id);
            header('Location: index.php?action=autores');
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=autores');
            exit;
        }
    }

    private function renderList(array $data = [], string $title = 'Autores'): void {
        $autores = $this->model->getAll();
        ResponseView::layout('autores/listar', ['autores' => $autores, ...$data], $title);
    }
}
