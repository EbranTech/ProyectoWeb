<?php
declare(strict_types=1);

namespace Core;

class ApiClient {
    private string $baseUrl;
    private string $token;

    public function __construct() {
        require_once __DIR__ . '/../config/env.php';
        $this->baseUrl = env('API_BASE_URL');
        $this->token = env('API_TOKEN');
    }

    public function request(string $method, string $endpoint, mixed $data = null, array $headers = []): array {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        $defaultHeaders = [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("API Request failed: " . curl_error($ch));
        }

        $decoded = json_decode($response, true);
        if (!$decoded) {
            throw new \RuntimeException("Invalid JSON response from API", $httpCode);
        }

        if (!$decoded['success']) {
            throw new \RuntimeException($decoded['message'] ?? "API Error", $httpCode);
        }

        return $decoded['data'] ?? [];
    }

    public function get(string $endpoint) { return $this->request('GET', $endpoint); }
    public function post(string $endpoint, array $data) { return $this->request('POST', $endpoint, $data); }
    public function put(string $endpoint, array $data) { return $this->request('PUT', $endpoint, $data); }
    public function delete(string $endpoint) { return $this->request('DELETE', $endpoint); }
}
