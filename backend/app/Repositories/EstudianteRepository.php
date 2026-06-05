<?php
declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

class EstudianteRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM estudiantes ORDER BY nombres, apellidos");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM estudiantes WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByCarnet(string $carnet): ?array {
        $stmt = $this->db->prepare("SELECT * FROM estudiantes WHERE carnet = ?");
        $stmt->execute([$carnet]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO estudiantes (carnet, nombres, apellidos, carrera, correo, telefono, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['carnet'],
            $data['nombres'],
            $data['apellidos'],
            $data['carrera'],
            $data['correo'],
            $data['telefono'],
            $data['estado'] ?? 'ACTIVO'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("UPDATE estudiantes SET carnet = ?, nombres = ?, apellidos = ?, carrera = ?, correo = ?, telefono = ?, estado = ? WHERE id_estudiante = ?");
        $stmt->execute([
            $data['carnet'],
            $data['nombres'],
            $data['apellidos'],
            $data['carrera'],
            $data['correo'],
            $data['telefono'],
            $data['estado'],
            $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM estudiantes WHERE id_estudiante = ?");
        $stmt->execute([$id]);
    }

    public function hasActiveLoans(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM prestamos WHERE id_estudiante = ? AND estado = 'ACTIVO'");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
