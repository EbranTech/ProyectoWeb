<?php
declare(strict_types=1);

namespace App\Middlewares;

use Core\Request;
use Core\Response;
use App\Repositories\UsuarioRepository;

class RoleMiddleware {
    private array $allowedRoles;

    public function __construct(array $allowedRoles = []) {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request): void {
        $username = $request->getHeader('x-user-username');
        if (!$username) {
            Response::error("User identification missing (X-User-Username header)", 401);
        }

        $repo = new UsuarioRepository();
        $user = $repo->findByUsername($username);

        if (!$user) {
            Response::error("User not found", 401);
        }

        // Fetch role name from DB
        $stmt = \Core\Database::getInstance()->prepare("SELECT nombre FROM roles WHERE id_rol = ?");
        $stmt->execute([$user['id_rol']]);
        $roleName = $stmt->fetchColumn();

        if (!in_array($roleName, $this->allowedRoles)) {
            Response::error("Forbidden: You do not have the required role ($roleName)", 403);
        }
    }
}
