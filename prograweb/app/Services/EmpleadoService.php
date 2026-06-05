<?php

declare(strict_types=1);

class EmpleadoService
{
    public function __construct(private EmpleadoRepository $empleadoRepository)
    {
    }

    public function list(): array
    {
        return $this->empleadoRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->empleadoRepository->findById($id);
    }

    public function create(array $data): array
    {
        if ($this->empleadoRepository->findByCorreo($data['correo']) !== false) {
            throw new RuntimeException('El correo ya existe para otro empleado');
        }

        $newEmployeeId = $this->empleadoRepository->create($data);

        return $this->empleadoRepository->findById($newEmployeeId) ?: [];
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->empleadoRepository->findById($id) === false) {
            return false;
        }

        if ($this->empleadoRepository->findByCorreoExceptId($data['correo'], $id) !== false) {
            throw new RuntimeException('El correo ya existe para otro empleado');
        }

        $this->empleadoRepository->update($id, $data);

        return $this->empleadoRepository->findById($id);
    }

    public function delete(int $id): bool
    {
        if ($this->empleadoRepository->findById($id) === false) {
            return false;
        }

        return $this->empleadoRepository->delete($id);
    }
}
