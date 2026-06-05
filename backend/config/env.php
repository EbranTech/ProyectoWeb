<?php
declare(strict_types=1);

function env(string $key, mixed $default = null): mixed {
    static $envVars = null;
    if ($envVars === null) {
        $envVars = [];
        $path = __DIR__ . '/../.env';
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                list($name, $value) = explode('=', $line, 2);
                $envVars[trim($name)] = trim($value);
            }
        }
    }
    return $envVars[$key] ?? $default;
}
