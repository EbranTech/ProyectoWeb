<?php
declare(strict_types=1);

namespace Models;

use Core\ApiClient;

class UsuarioApiModel {
    private ApiClient $api;

    public function __construct() {
        $this->api = new ApiClient();
    }

    public function getAll(): array { return $this->api->get('/usuarios'); }
    public function getById(int $id): array { return $this->api->get("/usuarios/$id"); }
    public function create(array $data): array { return $this->api->post('/usuarios', $data); }
    public function update(int $id, array $data): array { return $this->api->put("/usuarios/$id", $data); }
    public function delete(int $id): array { return $this->api->delete("/usuarios/$id"); }
}
