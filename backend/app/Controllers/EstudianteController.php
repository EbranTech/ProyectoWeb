<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\EstudianteService;
use App\Validators\EstudianteValidator;
use RuntimeException;

class EstudianteController {
    private EstudianteService $service;

    public function __construct() {
        $this->service = new EstudianteService();
    }

    public function index(Request $request): void {
        $students = $this->service->listAll();
        Response::success("Estudiantes recuperados", $students);
    }

    public function show(Request $request, int $id): void {
        $student = $this->service->getById($id);
        Response::success("Estudiante recuperado", $student);
    }

    public function lookupByCarnet(Request $request): void {
        $carnet = $request->getParam('carnet');
        if (!$carnet) Response::error("El carnet es requerido", 400);
        $student = $this->service->getByCarnet($carnet);
        if (!$student) Response::error("Estudiante no encontrado", 404);
        Response::success("Estudiante encontrado", $student);
    }

    public function create(Request $request): void {
        $data = $request->getBody();
        EstudianteValidator::validate($data);
        try {
            $id = $this->service->create($data);
            Response::success("Estudiante creado", ["id" => $id], 201);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): void {
        $data = $request->getBody();
        EstudianteValidator::validate($data);
        try {
            $this->service->update($id, $data);
            Response::success("Estudiante actualizado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function delete(Request $request, int $id): void {
        try {
            $this->service->delete($id);
            Response::success("Estudiante eliminado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
