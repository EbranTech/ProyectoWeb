<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\AutorService;
use App\Validators\AutorValidator;
use RuntimeException;

class AutorController {
    private AutorService $service;

    public function __construct() {
        $this->service = new AutorService();
    }

    public function index(Request $request): void {
        $autores = $this->service->listAll();
        Response::success("Autores recuperados", $autores);
    }

    public function show(Request $request, int $id): void {
        $autor = $this->service->getById($id);
        Response::success("Autor recuperado", $autor);
    }

    public function create(Request $request): void {
        $data = $request->getBody();
        AutorValidator::validate($data);
        try {
            $id = $this->service->create($data);
            Response::success("Autor creado", ["id" => $id], 201);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): void {
        $data = $request->getBody();
        AutorValidator::validate($data);
        try {
            $this->service->update($id, $data);
            Response::success("Autor actualizado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function delete(Request $request, int $id): void {
        try {
            $this->service->delete($id);
            Response::success("Autor eliminado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
