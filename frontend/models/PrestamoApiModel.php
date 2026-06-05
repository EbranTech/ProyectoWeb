<?php
declare(strict_types=1);

namespace Models;

use Core\ApiClient;

class PrestamoApiModel {
    private ApiClient $api;

    public function __construct() {
        $this->api = new ApiClient();
    }

    public function getAll(): array { return $this->api->get('/prestamos'); }
    public function create(array $data): array { return $this->api->post('/prestamos', $data); }
    public function returnLoan(array $data): array { return $this->api->post('/prestamos/return', $data); }
}
