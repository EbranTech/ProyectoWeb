<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\UsuarioService;
use App\Validators\UsuarioValidator;
use RuntimeException;

class UsuarioController {
    private UsuarioService $service;

    public function __construct() {
        $this->service = new UsuarioService();
    }

    public function index(Request $request): void {
        $users = $this->service->listAll();
        Response::success("Usuarios recuperados", $users);
    }

    public function show(Request $request, int $id): void {
        $user = $this->service->getById($id);
        Response::success("Usuario recuperado", $user);
    }

    public function create(Request $request): void {
        $data = $request->getBody();
        UsuarioValidator::validateCreate($data);
        try {
            $id = $this->service->create($data);
            Response::success("Usuario creado", ["id" => $id], 201);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): void {
        $data = $request->getBody();
        UsuarioValidator::validateUpdate($data);
        try {
            // Note: in a real system, currentUsername would come from a session or JWT token
            // For this architecture, we'll expect it in the request header or similar
            $currentUsername = $request->getHeader('x-user-username') ?? '';
            $user = $this->service->update($id, $data, $currentUsername);
            Response::success("Usuario actualizado", $user);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function delete(Request $request, int $id): void {
        try {
            $currentUsername = $request->getHeader('x-user-username') ?? '';
            $this->service->delete($id, $currentUsername);
            Response::success("Usuario eliminado");
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
