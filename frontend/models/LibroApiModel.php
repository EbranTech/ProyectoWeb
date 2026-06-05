<?php
declare(strict_types=1);

namespace Models;

use Core\ApiClient;

class LibroApiModel {
    private ApiClient $api;

    public function __construct() {
        $this->api = new ApiClient();
    }

    public function getAll(): array { return $this->api->get('/libros'); }
    public function getById(int $id): array { return $this->api->get("/libros/$id"); }
    public function lookupByIsbn(string $isbn): array { return $this->api->get('/libros/lookup', ['isbn' => $isbn]); }
    public function create(array $data): array { return $this->api->post('/libros', $data); }
    public function update(int $id, array $data): array { return $this->api->put("/libros/$id", $data); }
    public function delete(int $id): array { return $this->api->delete("/libros/$id"); }
}
