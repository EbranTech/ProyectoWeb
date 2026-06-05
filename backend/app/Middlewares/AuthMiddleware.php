<?php
declare(strict_types=1);

namespace App\Middlewares;

use Core\Request;
use Core\Response;

class AuthMiddleware {
    public function handle(Request $request): void {
        require_once __DIR__ . '/../../config/env.php';
        $token = $request->getHeader('authorization');

        if ($token && str_starts_with($token, 'Bearer ')) {
            $extractedToken = substr($token, 7);
            if ($extractedToken === env('API_TOKEN')) {
                return;
            }
        }

        Response::error("Unauthorized: Invalid or missing API token", 401);
    }
}
