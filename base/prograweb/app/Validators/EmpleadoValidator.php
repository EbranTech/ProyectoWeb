<?php

declare(strict_types=1);

class EmpleadoValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        $nombre = trim((string) ($data['nombre'] ?? ''));
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($nombre) > 100) {
            $errors['nombre'] = 'El nombre no puede superar 100 caracteres';
        }

        $apellido = trim((string) ($data['apellido'] ?? ''));
        if ($apellido === '') {
            $errors['apellido'] = 'El apellido es obligatorio';
        } elseif (strlen($apellido) > 100) {
            $errors['apellido'] = 'El apellido no puede superar 100 caracteres';
        }

        $correo = trim((string) ($data['correo'] ?? ''));
        if ($correo === '') {
            $errors['correo'] = 'El correo es obligatorio';
        } elseif (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            $errors['correo'] = 'El correo no tiene un formato valido';
        } elseif (strlen($correo) > 150) {
            $errors['correo'] = 'El correo no puede superar 150 caracteres';
        }

        $puesto = trim((string) ($data['puesto'] ?? ''));
        if ($puesto === '') {
            $errors['puesto'] = 'El puesto es obligatorio';
        } elseif (strlen($puesto) > 100) {
            $errors['puesto'] = 'El puesto no puede superar 100 caracteres';
        }

        $salario = $data['salario'] ?? null;
        if (!is_numeric($salario) || (float) $salario <= 0) {
            $errors['salario'] = 'El salario debe ser mayor que 0';
        } elseif ((float) $salario > 99999999.99) {
            $errors['salario'] = 'El salario supera el valor permitido';
        }

        $fechaContratacion = trim((string) ($data['fecha_contratacion'] ?? ''));
        if ($fechaContratacion === '') {
            $errors['fecha_contratacion'] = 'La fecha de contratacion es obligatoria';
        } elseif (!$this->isValidDate($fechaContratacion)) {
            $errors['fecha_contratacion'] = 'La fecha de contratacion debe usar el formato YYYY-MM-DD';
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $parsedDate = DateTime::createFromFormat('Y-m-d', $date);

        return $parsedDate instanceof DateTime && $parsedDate->format('Y-m-d') === $date;
    }
}
