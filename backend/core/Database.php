<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $config['DB_HOST'],
                $config['DB_PORT'],
                $config['DB_DATABASE'],
                $config['DB_CHARSET']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['DB_USERNAME'],
                    $config['DB_PASSWORD'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
