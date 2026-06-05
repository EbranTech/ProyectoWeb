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
            $busqueda = trim((string) ($_GET['busqueda'] ?? ''));

            if ($busqueda !== '') {
                $needle = function_exists('mb_strtolower')
                    ? mb_strtolower($busqueda, 'UTF-8')
                    : strtolower($busqueda);

                $prestamos = array_values(array_filter(
                    $prestamos,
                    static function (array $row) use ($needle): bool {
                        if (($row['estado'] ?? '') !== 'ACTIVO') {
                            return false;
                        }

                        $haystack = implode(' ', [
                            (string) ($row['carnet'] ?? ''),
                            (string) ($row['estudiante'] ?? ''),
                            (string) ($row['libro'] ?? ''),
                        ]);

                        $haystack = function_exists('mb_strtolower')
                            ? mb_strtolower($haystack, 'UTF-8')
                            : strtolower($haystack);

                        return str_contains($haystack, $needle);
                    }
                ));
            }

            ResponseView::layout('devoluciones/listar', [
                'prestamos' => $prestamos,
                'busqueda' => $busqueda,
            ], 'Devoluciones');
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            ResponseView::layout('devoluciones/listar', [
                'prestamos' => [],
                'busqueda' => trim((string) ($_GET['busqueda'] ?? '')),
            ], 'Devoluciones');
        }
    }

    public function returnLoan(): void {
        $redirectAction = $_POST['redirect_action'] ?? 'devoluciones';
        $redirectQuery = trim((string) ($_POST['redirect_query'] ?? ''));
        if (!in_array($redirectAction, ['prestamos', 'devoluciones'], true)) {
            $redirectAction = 'devoluciones';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->prestamoModel->returnLoan([
                    'id_prestamo' => $_POST['id_prestamo'],
                    'fecha_devolucion' => $_POST['fecha_devolucion']
                ]);
                $location = 'index.php?action=' . $redirectAction;
                if ($redirectAction === 'devoluciones' && $redirectQuery !== '') {
                    $location .= '&busqueda=' . rawurlencode($redirectQuery);
                }
                header('Location: ' . $location);
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
