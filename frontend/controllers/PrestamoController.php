<?php
declare(strict_types=1);

namespace Controllers;

use Models\PrestamoApiModel;
use Models\EstudianteApiModel;
use Models\LibroApiModel;
use Core\ResponseView;
use RuntimeException;

class PrestamoController {
    private PrestamoApiModel $prestamoModel;
    private EstudianteApiModel $estudianteModel;
    private LibroApiModel $libroModel;

    public function __construct() {
        $this->prestamoModel = new PrestamoApiModel();
        $this->estudianteModel = new EstudianteApiModel();
        $this->libroModel = new LibroApiModel();
    }

    public function index(): void {
        try {
            $this->renderLoanDashboard();
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('prestamos/listar', [
                'prestamos' => [],
                'estudiantes' => [],
                'libros' => [],
            ], 'Prestamos');
        }
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->prestamoModel->create($_POST);
                header('Location: index.php?action=prestamos');
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->renderLoanDashboard(['formData' => $_POST]);
                return;
            }
        }
        $this->renderLoanDashboard();
    }

    public function devoluciones(): void {
        try {
            $prestamos = $this->prestamoModel->getAll();
            ResponseView::layout('devoluciones/listar', ['prestamos' => $prestamos], 'Devoluciones');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('devoluciones/listar', ['prestamos' => []], 'Devoluciones');
        }
    }

    public function returnLoan(): void {
        $redirectAction = $_POST['redirect_action'] ?? 'devoluciones';
        if (!in_array($redirectAction, ['prestamos', 'devoluciones'], true)) {
            $redirectAction = 'devoluciones';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->prestamoModel->returnLoan([
                    'id_prestamo' => $_POST['id_prestamo'],
                    'fecha_devolucion' => $_POST['fecha_devolucion']
                ]);
                header('Location: index.php?action=' . $redirectAction);
                exit;
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        if ($redirectAction === 'prestamos') {
            $this->index();
            return;
        }

        $this->devoluciones();
    }

    public function historial(): void {
        try {
            $prestamos = $this->prestamoModel->getAll();
            ResponseView::layout('historial/listar', ['prestamos' => $prestamos], 'Historial de Prestamos');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('historial/listar', ['prestamos' => []], 'Historial de Prestamos');
        }
    }

    private function renderLoanDashboard(array $data = [], string $title = 'Prestamos'): void {
        $prestamos = $this->prestamoModel->getAll();
        $estudiantes = $this->estudianteModel->getAll();
        $libros = $this->libroModel->getAll();

        ResponseView::layout('prestamos/listar', [
            'prestamos' => $prestamos,
            'estudiantes' => $estudiantes,
            'libros' => $libros,
            ...$data,
        ], $title);
    }
}
