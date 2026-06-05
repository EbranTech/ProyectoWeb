<?php
declare(strict_types=1);

namespace App\Validators;

use RuntimeException;

class LibroValidator {
    public static function validate(array $data): void {
        if (empty($data['codigo']) || empty($data['isbn']) || empty($data['titulo']) || empty($data['id_autor']) || empty($data['total'])) {
            throw new RuntimeException("Codigo, ISBN, titulo, autor y cantidad total son requeridos", 400);
        }
        if ((int)$data['total'] < 1) {
            throw new RuntimeException("La cantidad total debe ser al menos 1", 400);
        }
    }
}
