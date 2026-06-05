<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AutorRepository;
use RuntimeException;

class AutorService {
    private AutorRepository $repository;

    public function __construct() {
        $this->repository = new AutorRepository();
    }

    public function listAll(): array {
        return $this->repository->getAll();
    }

    public function getById(int $id): array {
        $autor = $this->repository->findById($id);
        if (!$autor) throw new RuntimeException("Autor no encontrado", 404);
        return $autor;
    }

    public function create(array $data): int {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): void {
        $this->getById($id);
        $this->repository->update($id, $data);
    }

    public function delete(int $id): void {
        if ($this->repository->hasBooks($id)) {
            throw new RuntimeException("No se puede eliminar un autor asociado a libros", 400);
        }
        $this->repository->delete($id);
    }
}
