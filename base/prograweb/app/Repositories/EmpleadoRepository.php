<?php

declare(strict_types=1);

class EmpleadoRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM empleados ORDER BY id DESC');

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM empleados WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function findByCorreo(string $correo): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM empleados WHERE correo = :correo LIMIT 1');
        $statement->execute(['correo' => $correo]);

        return $statement->fetch();
    }

    public function findByCorreoExceptId(string $correo, int $id): array|false
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM empleados WHERE correo = :correo AND id <> :id LIMIT 1'
        );
        $statement->execute([
            'correo' => $correo,
            'id' => $id,
        ]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO empleados (nombre, apellido, correo, puesto, salario, fecha_contratacion)
             VALUES (:nombre, :apellido, :correo, :puesto, :salario, :fecha_contratacion)'
        );

        $statement->execute([
            'nombre' => trim((string) $data['nombre']),
            'apellido' => trim((string) $data['apellido']),
            'correo' => trim((string) $data['correo']),
            'puesto' => trim((string) $data['puesto']),
            'salario' => (float) $data['salario'],
            'fecha_contratacion' => trim((string) $data['fecha_contratacion']),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE empleados
             SET nombre = :nombre,
                 apellido = :apellido,
                 correo = :correo,
                 puesto = :puesto,
                 salario = :salario,
                 fecha_contratacion = :fecha_contratacion
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'nombre' => trim((string) $data['nombre']),
            'apellido' => trim((string) $data['apellido']),
            'correo' => trim((string) $data['correo']),
            'puesto' => trim((string) $data['puesto']),
            'salario' => (float) $data['salario'],
            'fecha_contratacion' => trim((string) $data['fecha_contratacion']),
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM empleados WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }
}
