<?php
declare(strict_types=1);

namespace Core;

class Request {
    private string $method;
    private string $uri;
    private array $params;
    private array $headers;
    private mixed $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->params = $_GET;
        $this->headers = $this->getHeaders();
        $this->body = $this->parseBody();
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getParam(string $key, mixed $default = null): mixed {
        return $this->params[$key] ?? $default;
    }

    public function getHeader(string $key): ?string {
        return $this->headers[$key] ?? null;
    }

    public function getBody(): mixed {
        return $this->body;
    }

    private function getHeaders(): array {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('HTTP_', '', $key);
                $name = str_replace('_', '-', $name);
                $headers[strtolower($name)] = $value;
            }
        }

        if (function_exists('apache_request_headers')) {
            foreach (apache_request_headers() as $name => $value) {
                $headers[strtolower($name)] = $value;
            }
        }

        foreach (['REDIRECT_HTTP_AUTHORIZATION', 'Authorization'] as $serverKey) {
            if (isset($_SERVER[$serverKey]) && !isset($headers['authorization'])) {
                $headers['authorization'] = $_SERVER[$serverKey];
            }
        }

        // Manually add Content-Type since it's not HTTP_
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        return $headers;
    }

    private function parseBody(): mixed {
        $contentType = $this->headers['content-type'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawBody = file_get_contents('php://input');
            return json_decode($rawBody, true);
        }
        return $_POST;
    }
}
