<?php
declare(strict_types=1);

namespace Controllers;

use Models\EstudianteApiModel;
use Core\ResponseView;
use RuntimeException;

class EstudianteController {
    private EstudianteApiModel $model;

    public function __construct() {
        $this->model = new EstudianteApiModel();
    }

    public function index(): void {
        try {
            $this->renderList();
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('estudiantes/listar', ['estudiantes' => []], 'Estudiantes');
        }
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create($_POST);
                header('Location: index.php?action=estudiantes');
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
            $estudiante = $this->model->getById($id);
            $this->renderList(['estudiante' => $estudiante], 'Editar Estudiante');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=estudiantes');
            exit;
        }
    }

    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->update($id, $_POST);
                header('Location: index.php?action=estudiantes');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->renderList([
                    'estudiante' => ['id_estudiante' => $id] + $_POST,
                    'formData' => $_POST,
                ], 'Editar Estudiante');
                return;
            }
        }
        $this->edit($id);
    }

    public function delete(int $id): void {
        try {
            $this->model->delete($id);
            header('Location: index.php?action=estudiantes');
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=estudiantes');
            exit;
        }
    }

    private function renderList(array $data = [], string $title = 'Estudiantes'): void {
        $estudiantes = $this->model->getAll();
        ResponseView::layout('estudiantes/listar', ['estudiantes' => $estudiantes, ...$data], $title);
    }
}
