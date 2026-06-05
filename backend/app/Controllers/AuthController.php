<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\AuthService;
use RuntimeException;

class AuthController {
    private AuthService $service;

    public function __construct() {
        $this->service = new AuthService();
    }

    public function login(Request $request): void {
        $data = $request->getBody();

        if (empty($data['username']) || empty($data['password'])) {
            Response::error("Usuario y contraseña son requeridos", 400);
        }

        try {
            $user = $this->service->authenticate($data['username'], $data['password']);
            Response::success("Autenticación exitosa", $user);
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 401);
        }
    }
}
