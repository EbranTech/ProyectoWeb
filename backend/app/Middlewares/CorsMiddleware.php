<?php
declare(strict_types=1);

namespace App\Middlewares;

use Core\Request;
use Core\Response;

class CorsMiddleware {
    public function handle(Request $request): void {
        require_once __DIR__ . '/../../config/env.php';
        $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', '*'));
        $origin = $request->getHeader('origin');

        if ($origin && in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($request->getMethod() === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
