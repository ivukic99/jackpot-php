<?php
require_once 'ResponseHelper.php';

class Middleware {
    public static function apiKeyAuthenticate() {
        $config = include __DIR__ . '/../config/config.php';
        $apiKey = $config['API_KEY'];
        $headers = getallheaders();

        if (!isset($headers['X-API-Key']) || $headers['X-API-Key'] !== $apiKey) {
            ResponseHelper::jsonResponse(['error' => 'Invalid API Key.'], 403);
        }
    }

    public static function ensureJsonRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $content_type = $_SERVER['CONTENT_TYPE'];
            if ($content_type !== 'application/json') {
                ResponseHelper::jsonResponse(["error" => "Content-Type is missing or invalid. Expected application/json."], 415);
            }
        }
    }
}