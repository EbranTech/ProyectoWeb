<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\PrestamoService;
use App\Validators\PrestamoValidator;
use RuntimeException;

class PrestamoController {
    private PrestamoService $service;

    public function __construct() {
        $this->service = new PrestamoService();
    }

    public function index(Request $request): void {
        $prestamos = $this->service->listAll();
        Response::success("Prestamos recuperados", $prestamos);
    }

    public function create(Request $request): void {
        $data = $request->getBody();
        PrestamoValidator::validateLoan($data);
        try {
            $id = $this->service->createLoan($data);
            Response::success("Prestamo registrado", ["id" => $id], 201);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function returnLoan(Request $request): void {
        $data = $request->getBody();
        PrestamoValidator::validateReturn($data);
        try {
            $this->service->returnLoan((int)$data['id_prestamo'], $data['fecha_devolucion']);
            Response::success("Devolucion registrada");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
