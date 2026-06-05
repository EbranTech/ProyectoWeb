<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UsuarioRepository;
use RuntimeException;

class AuthService {
    private UsuarioRepository $repository;

    public function __construct() {
        $this->repository = new UsuarioRepository();
    }

    public function authenticate(string $username, string $password): array {
        $user = $this->repository->findByUsername($username);

        if (!$user) {
            throw new RuntimeException("Credenciales incorrectas", 401);
        }

        if (!$user['activo']) {
            throw new RuntimeException("El usuario está desactivado", 401);
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new RuntimeException("Credenciales incorrectas", 401);
        }

        // Retornamos solo la información necesaria para la sesión, NO la contraseña
        return [
            'id_usuario' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'username' => $user['username'],
            'rol' => $this->getRoleName($user['id_rol'])
        ];
    }

    private function getRoleName(int $idRol): string {
        return $idRol === 1 ? 'ADMIN' : 'BIBLIOTECARIO';
    }
}
