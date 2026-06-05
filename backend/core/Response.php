<?php
declare(strict_types=1);

namespace Core;

class Response {
    public static function json(mixed $data, int $status = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $status = 400, array $errors = []): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public static function success(string $message, mixed $data = null, int $status = 200): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
