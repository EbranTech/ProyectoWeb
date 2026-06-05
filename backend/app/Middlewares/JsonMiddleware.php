<?php
declare(strict_types=1);

namespace App\Middlewares;

use Core\Request;

class JsonMiddleware {
    public function handle(Request $request): void {
        header('Content-Type: application/json; charset=utf-8');
    }
}
