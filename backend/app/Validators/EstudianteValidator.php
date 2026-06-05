<?php
declare(strict_types=1);

namespace App\Validators;

use RuntimeException;

class EstudianteValidator {
    public static function validate(array $data): void {
        if (empty($data['carnet']) || empty($data['nombres']) || empty($data['apellidos']) || empty($data['carrera'])) {
            throw new RuntimeException("Carnet, nombres, apellidos y carrera son requeridos", 400);
        }
    }
}
