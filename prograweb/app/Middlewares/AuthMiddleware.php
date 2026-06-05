<?php

declare(strict_types=1);

class AuthMiddleware implements Middleware
{
    private string $expectedToken = 'Bearer 123456';

    public function handle(Request $request): void
    {
        $authorizationHeader = $request->getHeader('Authorization');

        if ($authorizationHeader !== $this->expectedToken) {
            Response::json(
                [
                    'success' => false,
                    'message' => 'No autorizado',
                ],
                401
            );
            exit;
        }
    }
}
