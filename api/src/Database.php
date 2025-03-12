<?php
    class Database {
        private static $pdo = null;

        public static function connect() {
            if (!self::$pdo) {
                try {
                    $config = include __DIR__ . '/../config/config.php';
                    self::$pdo = new PDO("mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']}", $config['DB_USER'], $config['DB_PASSWORD']);
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(["message" => "Connection faild: " . $e->getMessage()]);
                    exit;
                }
            }
            return self::$pdo;
        }
    }
