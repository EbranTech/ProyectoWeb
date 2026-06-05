<?php
declare(strict_types=1);

namespace App\Validators;

use RuntimeException;

class PrestamoValidator {
    public static function validateLoan(array $data): void {
        if (empty($data['carnet']) || empty($data['isbn']) || empty($data['fecha_prestamo']) || empty($data['fecha_esperada'])) {
            throw new RuntimeException("Carnet, ISBN y fechas son requeridos", 400);
        }
    }

    public static function validateReturn(array $data): void {
        if (empty($data['id_prestamo']) || empty($data['fecha_devolucion'])) {
            throw new RuntimeException("ID de prestamo y fecha de devolucion son requeridos", 400);
        }
    }
}
