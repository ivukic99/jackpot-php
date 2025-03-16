<?php
require_once 'ResponseHelper.php';
require_once 'ErrorLogger.php';
class Database {
    private static $pdo = null;
    public static function connect(): ?PDO
    {
        if (!self::$pdo) {
            try {
                $config = include __DIR__ . '/../config/config.php';
                self::$pdo = new PDO("mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']}", $config['DB_USER'], $config['DB_PASSWORD']);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                ErrorLogger::error("Connection failed: " . $e->getMessage(), $e->getFile(), $e->getLine());
                ResponseHelper::jsonResponse(["message" => "Connection failed: " . $e->getMessage()], 500);
            }
        }
        return self::$pdo;
    }
}
