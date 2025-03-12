<?php
require_once 'ResponseHelper.php';

class ApiKeyMiddleware {
    public static function authenticate() {
        $config = include __DIR__ . '/../config/config.php';
        $apiKey = $config['API_KEY'];
        $headers = getallheaders();

        if (!isset($headers['X-API-Key']) || $headers['X-API-Key'] !== $apiKey) {
            ResponseHelper::jsonResponse(['error' => 'Invalid API Key.'], 403);
        }
    }
}