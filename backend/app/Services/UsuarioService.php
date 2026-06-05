<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UsuarioRepository;
use RuntimeException;

class UsuarioService {
    private UsuarioRepository $repository;

    public function __construct() {
        $this->repository = new UsuarioRepository();
    }

    public function listAll(): array {
        return $this->repository->getAll();
    }

    public function getById(int $id): array {
        $user = $this->repository->findById($id);
        if (!$user) throw new RuntimeException("Usuario no encontrado", 404);
        return $user;
    }

    public function create(array $data): int {
        if ($this->repository->findByUsername($data['username'])) {
            throw new RuntimeException("Ya existe un usuario con ese username", 400);
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->repository->create([
            'id_rol' => (int)$data['id_rol'],
            'nombre' => $data['nombre'],
            'username' => $data['username'],
            'password_hash' => $passwordHash,
            'activo' => (bool)$data['activo']
        ]);
    }

    public function update(int $id, array $data, string $currentUsername): array {
        $user = $this->getById($id);

        if ($this->repository->findByUsername($data['username']) && $user['username'] !== $data['username']) {
            throw new RuntimeException("Ya existe un usuario con ese username", 400);
        }

        // Check if current user is removing their own admin access
        if ($user['username'] === $currentUsername &&
            ($data['id_rol'] != 1 || $data['activo'] == 0)) {
            throw new RuntimeException("No puede quitar su propio acceso administrador", 400);
        }

        // Check if this is the last active admin
        if ($user['id_rol'] == 1 && $user['activo'] == 1 &&
            ($data['id_rol'] != 1 || $data['activo'] == 0)) {
            if ($this->repository->countActiveAdminsExcluding($id) === 0) {
                throw new RuntimeException("Debe quedar al menos un administrador activo", 400);
            }
        }

        $passwordHash = $user['password_hash'];
        if (!empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->repository->update($id, [
            'nombre' => $data['nombre'],
            'username' => $data['username'],
            'password_hash' => $passwordHash,
            'id_rol' => (int)$data['id_rol'],
            'activo' => (bool)$data['activo']
        ]);

        return $this->getById($id);
    }

    public function delete(int $id, string $currentUsername): void {
        $user = $this->getById($id);
        if ($user['username'] === $currentUsername) {
            throw new RuntimeException("No puede eliminar el usuario en sesion", 400);
        }
        if ($user['id_rol'] == 1 && $user['activo'] == 1 && $this->repository->countActiveAdminsExcluding($id) === 0) {
            throw new RuntimeException("Debe quedar al menos un administrador activo", 400);
        }
        $this->repository->delete($id);
    }
}
