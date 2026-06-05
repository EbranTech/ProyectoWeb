<?php
declare(strict_types=1);

namespace Models;

use Core\ApiClient;

class AutorApiModel {
    private ApiClient $api;

    public function __construct() {
        $this->api = new ApiClient();
    }

    public function getAll(): array { return $this->api->get('/autores'); }
    public function getById(int $id): array { return $this->api->get("/autores/$id"); }
    public function create(array $data): array { return $this->api->post('/autores', $data); }
    public function update(int $id, array $data): array { return $this->api->put("/autores/$id", $data); }
    public function delete(int $id): array { return $this->api->delete("/autores/$id"); }
}
