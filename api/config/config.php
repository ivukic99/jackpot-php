<?php
require __DIR__ . '/../vendor/autoload.php';

$envPathLocal = dirname(__DIR__, 2) . '/.env';
$envPathDocker = dirname(__DIR__, 1) . '/.env';

if (file_exists($envPathLocal)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();
} elseif (file_exists($envPathDocker)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();
} else {
    http_response_code(403);
    echo json_encode(["error" => ".env file not found."]);
    exit;
}

return [
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'DB_USER' => $_ENV['DB_USER'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'],
    'API_KEY' => $_ENV['API_KEY']
];
