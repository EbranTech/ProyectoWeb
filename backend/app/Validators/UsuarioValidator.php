<?php
declare(strict_types=1);

namespace App\Validators;

use RuntimeException;

class UsuarioValidator {
    public static function validateCreate(array $data): void {
        if (empty($data['nombre']) || empty($data['username']) || empty($data['password'])) {
            throw new RuntimeException("Nombre, usuario y contraseña son requeridos", 400);
        }
        if (empty($data['id_rol'])) {
            throw new RuntimeException("El rol es requerido", 400);
        }
    }

    public static function validateUpdate(array $data): void {
        if (empty($data['nombre']) || empty($data['username'])) {
            throw new RuntimeException("Nombre y usuario son requeridos", 400);
        }
        if (empty($data['id_rol'])) {
            throw new RuntimeException("El rol es requerido", 400);
        }
    }
}
