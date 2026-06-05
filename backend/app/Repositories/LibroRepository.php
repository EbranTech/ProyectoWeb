<?php
declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

class LibroRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT l.*, CONCAT(a.nombres, ' ', a.apellidos) as autor
            FROM libros l
            JOIN autores a ON l.id_autor = a.id_autor
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE id_libro = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByIsbn(string $isbn): ?array {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE isbn = ?");
        $stmt->execute([$isbn]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO libros (codigo_libro, isbn, titulo, id_autor, categoria, editorial, anio_publicacion, cantidad_total, cantidad_disponible, ubicacion, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['codigo'],
            $data['isbn'],
            $data['titulo'],
            $data['id_autor'],
            $data['categoria'],
            $data['editorial'],
            $data['anio'],
            $data['total'],
            $data['disponibles'],
            $data['ubicacion'],
            $data['estado']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE libros SET codigo_libro = ?, isbn = ?, titulo = ?, id_autor = ?, categoria = ?, editorial = ?, anio_publicacion = ?, cantidad_total = ?, cantidad_disponible = ?, ubicacion = ?, estado = ?
            WHERE id_libro = ?
        ");
        $stmt->execute([
            $data['codigo'],
            $data['isbn'],
            $data['titulo'],
            $data['id_autor'],
            $data['categoria'],
            $data['editorial'],
            $data['anio'],
            $data['total'],
            $data['disponibles'],
            $data['ubicacion'],
            $data['estado'],
            $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM libros WHERE id_libro = ?");
        $stmt->execute([$id]);
    }

    public function countActiveLoans(int $id): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM prestamos WHERE id_libro = ? AND estado = 'ACTIVO'");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }
}
