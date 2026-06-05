<?php
declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

class UsuarioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT u.*, r.nombre as rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT u.*, r.nombre as rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO usuarios (id_rol, nombre, username, password_hash, activo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['id_rol'],
            $data['nombre'],
            $data['username'],
            $data['password_hash'],
            $data['activo'] ?? 1
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, username = ?, password_hash = ?, id_rol = ?, activo = ? WHERE id_usuario = ?");
        $stmt->execute([
            $data['nombre'],
            $data['username'],
            $data['password_hash'],
            $data['id_rol'],
            $data['activo'],
            $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
    }

    public function countActiveAdminsExcluding(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE r.nombre = 'ADMIN' AND u.activo = 1 AND u.id_usuario != ?
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
