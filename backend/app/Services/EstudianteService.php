<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\EstudianteRepository;
use RuntimeException;

class EstudianteService {
    private EstudianteRepository $repository;

    public function __construct() {
        $this->repository = new EstudianteRepository();
    }

    public function listAll(): array {
        return $this->repository->getAll();
    }

    public function getById(int $id): array {
        $student = $this->repository->findById($id);
        if (!$student) throw new RuntimeException("Estudiante no encontrado", 404);
        return $student;
    }

    public function getByCarnet(string $carnet): ?array {
        return $this->repository->findByCarnet($carnet);
    }

    public function create(array $data): int {
        if ($this->repository->findByCarnet($data['carnet'])) {
            throw new RuntimeException("Ya existe un estudiante con ese carnet", 400);
        }
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): void {
        $this->getById($id);
        if ($this->repository->findByCarnet($data['carnet']) && $this->repository->findById($id)['carnet'] !== $data['carnet']) {
            throw new RuntimeException("Ya existe un estudiante con ese carnet", 400);
        }
        $this->repository->update($id, $data);
    }

    public function delete(int $id): void {
        if ($this->repository->hasActiveLoans($id)) {
            throw new RuntimeException("No se puede eliminar un estudiante con prestamos activos", 400);
        }
        $this->repository->delete($id);
    }
}
