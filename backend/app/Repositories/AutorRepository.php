<?php
declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

class AutorRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM autores ORDER BY nombres, apellidos");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM autores WHERE id_autor = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO autores (nombres, apellidos, nacionalidad) VALUES (?, ?, ?)");
        $stmt->execute([$data['nombres'], $data['apellidos'], $data['nacionalidad']]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("UPDATE autores SET nombres = ?, apellidos = ?, nacionalidad = ? WHERE id_autor = ?");
        $stmt->execute([$data['nombres'], $data['apellidos'], $data['nacionalidad'], $id]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM autores WHERE id_autor = ?");
        $stmt->execute([$id]);
    }

    public function hasBooks(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM libros WHERE id_autor = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
