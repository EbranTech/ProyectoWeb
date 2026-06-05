<?php
declare(strict_types=1);

namespace App\Validators;

use RuntimeException;

class AutorValidator {
    public static function validate(array $data): void {
        if (empty($data['nombres']) || empty($data['apellidos'])) {
            throw new RuntimeException("Nombres y apellidos son requeridos", 400);
        }
    }
}
