<?php
require __DIR__ . '/../vendor/autoload.php';

$envPath = dirname(__DIR__, 1) . '/.env';
if (!file_exists($envPath)) {
    echo json_encode(["error" => ".env file not found at $envPath"]);
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

return [
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'DB_USER' => $_ENV['DB_USER'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD']
]
?>