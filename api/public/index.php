<?php
require_once '../vendor/autoload.php';
require_once '../src/Middleware.php';
require_once '../src/ResponseHelper.php';

try {
    $router = require __DIR__ . '/../routes/routes.php';

    Middleware::apiKeyAuthenticate();
    Middleware::ensureJsonRequest();

    $router->handleRequest();
} catch (Throwable $e) {
    ResponseHelper::jsonResponse(["error" => "{$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}" ], 500);
}
