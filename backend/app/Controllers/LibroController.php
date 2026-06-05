<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\LibroService;
use App\Validators\LibroValidator;
use RuntimeException;

class LibroController {
    private LibroService $service;

    public function __construct() {
        $this->service = new LibroService();
    }

    public function index(Request $request): void {
        $libros = $this->service->listAll();
        Response::success("Libros recuperados", $libros);
    }

    public function show(Request $request, int $id): void {
        $libro = $this->service->getById($id);
        Response::success("Libro recuperado", $libro);
    }

    public function lookupByIsbn(Request $request): void {
        $isbn = $request->getParam('isbn');
        if (!$isbn) Response::error("El ISBN es requerido", 400);
        $libro = $this->service->getByIsbn($isbn);
        if (!$libro) Response::error("Libro no encontrado", 404);
        Response::success("Libro encontrado", $libro);
    }

    public function create(Request $request): void {
        $data = $request->getBody();
        LibroValidator::validate($data);
        try {
            $id = $this->service->create($data);
            Response::success("Libro creado", ["id" => $id], 201);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): void {
        $data = $request->getBody();
        LibroValidator::validate($data);
        try {
            $this->service->update($id, $data);
            Response::success("Libro actualizado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function delete(Request $request, int $id): void {
        try {
            $this->service->delete($id);
            Response::success("Libro eliminado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
