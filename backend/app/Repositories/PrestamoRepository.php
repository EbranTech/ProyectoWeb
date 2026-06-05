<?php
declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

class PrestamoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT p.*, e.carnet, CONCAT(e.nombres, ' ', e.apellidos) as estudiante, e.carrera,
                   l.codigo_libro, l.titulo as libro, CONCAT(a.nombres, ' ', a.apellidos) as autor,
                   l.ubicacion
            FROM prestamos p
            JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
            JOIN libros l ON p.id_libro = l.id_libro
            JOIN autores a ON l.id_autor = a.id_autor
            ORDER BY p.id_prestamo DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM prestamos WHERE id_prestamo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO prestamos (id_estudiante, id_libro, fecha_prestamo, fecha_devolucion_esperada, estado, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['id_estudiante'],
            $data['id_libro'],
            $data['fecha_prestamo'],
            $data['fecha_esperada'],
            $data['estado'] ?? 'ACTIVO',
            $data['observaciones']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function returnBook(int $id, string $fechaDevolucion): void {
        $stmt = $this->db->prepare("UPDATE prestamos SET estado = 'DEVUELTO', fecha_devolucion_real = ? WHERE id_prestamo = ?");
        $stmt->execute([$fechaDevolucion, $id]);
    }

    public function updateBookInventory(int $libroId, int $delta): void {
        $stmt = $this->db->prepare("UPDATE libros SET cantidad_disponible = cantidad_disponible + ? WHERE id_libro = ?");
        $stmt->execute([$delta, $libroId]);
    }

    public function getLibroId(int $prestamoId): ?int {
        $stmt = $this->db->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt->execute([$prestamoId]);
        $res = $stmt->fetch();
        return $res ? (int)$res['id_libro'] : null;
    }
}
