<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'DB_HOST'     => env('DB_HOST', 'localhost'),
    'DB_PORT'     => env('DB_PORT', '3306'),
    'DB_DATABASE' => env('DB_DATABASE', 'bibliosys'),
    'DB_USERNAME' => env('DB_USERNAME', 'root'),
    'DB_PASSWORD' => env('DB_PASSWORD', ''),
    'DB_CHARSET'  => env('DB_CHARSET', 'utf8mb4'),
];
