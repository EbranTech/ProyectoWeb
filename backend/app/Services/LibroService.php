<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\LibroRepository;
use RuntimeException;

class LibroService {
    private LibroRepository $repository;

    public function __construct() {
        $this->repository = new LibroRepository();
    }

    public function listAll(): array {
        return $this->repository->getAll();
    }

    public function getById(int $id): array {
        $libro = $this->repository->findById($id);
        if (!$libro) throw new RuntimeException("Libro no encontrado", 404);
        return $libro;
    }

    public function getByIsbn(string $isbn): ?array {
        return $this->repository->findByIsbn($isbn);
    }

    public function create(array $data): int {
        if ($this->repository->findByIsbn($data['isbn'])) {
            throw new RuntimeException("Ya existe un libro con ese ISBN", 400);
        }

        $estado = $data['estado'] ?? 'DISPONIBLE';
        $disponibles = ($estado === 'MANTENIMIENTO') ? 0 : $data['total'];

        return $this->repository->create([
            'codigo' => $data['codigo'],
            'isbn' => $data['isbn'],
            'titulo' => $data['titulo'],
            'id_autor' => (int)$data['id_autor'],
            'categoria' => $data['categoria'],
            'editorial' => $data['editorial'],
            'anio' => (int)$data['anio'],
            'total' => (int)$data['total'],
            'disponibles' => $disponibles,
            'ubicacion' => $data['ubicacion'],
            'estado' => $estado
        ]);
    }

    public function update(int $id, array $data): void {
        $this->getById($id);
        $activeLoans = $this->repository->countActiveLoans($id);

        if ((int)$data['total'] < $activeLoans) {
            throw new RuntimeException("La cantidad total no puede ser menor a los prestamos activos", 400);
        }

        $estado = $data['estado'] ?? 'DISPONIBLE';
        $disponibles = ($estado === 'MANTENIMIENTO') ? 0 : max(0, (int)$data['total'] - $activeLoans);

        $this->repository->update($id, [
            'codigo' => $data['codigo'],
            'isbn' => $data['isbn'],
            'titulo' => $data['titulo'],
            'id_autor' => (int)$data['id_autor'],
            'categoria' => $data['categoria'],
            'editorial' => $data['editorial'],
            'anio' => (int)$data['anio'],
            'total' => (int)$data['total'],
            'disponibles' => $disponibles,
            'ubicacion' => $data['ubicacion'],
            'estado' => $estado
        ]);
    }

    public function delete(int $id): void {
        if ($this->repository->countActiveLoans($id) > 0) {
            throw new RuntimeException("No se puede eliminar un libro con prestamos activos", 400);
        }
        $this->repository->delete($id);
    }
}
