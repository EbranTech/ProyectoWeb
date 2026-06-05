<?php
declare(strict_types=1);

namespace Models;

use Core\ApiClient;

class EstudianteApiModel {
    private ApiClient $api;

    public function __construct() {
        $this->api = new ApiClient();
    }

    public function getAll(): array { return $this->api->get('/estudiantes'); }
    public function getById(int $id): array { return $this->api->get("/estudiantes/$id"); }
    public function lookupByCarnet(string $carnet): array { return $this->api->get('/estudiantes/lookup', ['carnet' => $carnet]); }
    public function create(array $data): array { return $this->api->post('/estudiantes', $data); }
    public function update(int $id, array $data): array { return $this->api->put("/estudiantes/$id", $data); }
    public function delete(int $id): array { return $this->api->delete("/estudiantes/$id"); }
}
