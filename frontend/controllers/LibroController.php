<?php
declare(strict_types=1);

namespace Controllers;

use Models\LibroApiModel;
use Models\AutorApiModel;
use Core\ResponseView;
use RuntimeException;

class LibroController {
    private LibroApiModel $libroModel;
    private AutorApiModel $autorModel;

    public function __construct() {
        $this->libroModel = new LibroApiModel();
        $this->autorModel = new AutorApiModel();
    }

    public function index(): void {
        try {
            $this->renderList();
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('libros/listar', ['libros' => [], 'autores' => []], 'Libros');
        }
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->libroModel->create($_POST);
                header('Location: index.php?action=libros');
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
            $libro = $this->libroModel->getById($id);
            $this->renderList(['libro' => $libro], 'Editar Libro');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=libros');
            exit;
        }
    }

    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->libroModel->update($id, $_POST);
                header('Location: index.php?action=libros');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->renderList([
                    'libro' => ['id_libro' => $id] + $_POST,
                    'formData' => $_POST,
                ], 'Editar Libro');
                return;
            }
        }
        $this->edit($id);
    }

    public function delete(int $id): void {
        try {
            $this->libroModel->delete($id);
            header('Location: index.php?action=libros');
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=libros');
            exit;
        }
    }

    public function consulta(): void {
        try {
            $libros = $this->libroModel->getAll();
            ResponseView::layout('consulta/listar', ['libros' => $libros], 'Consulta de Libros');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('consulta/listar', ['libros' => []], 'Consulta de Libros');
        }
    }

    private function renderList(array $data = [], string $title = 'Libros'): void {
        $libros = $this->libroModel->getAll();
        $autores = $this->autorModel->getAll();
        ResponseView::layout('libros/listar', ['libros' => $libros, 'autores' => $autores, ...$data], $title);
    }
}
