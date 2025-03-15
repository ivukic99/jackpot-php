<?php
require_once 'ResponseHelper.php';
class Router {
    private $routes = [];

    public function addRoute(string $method, string $uri, callable $handler): void
    {
        $this->routes[$method][$uri] = $handler;
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $handler = $this->routes[$method][$uri] ?? null;

        if (!$handler) {
            ResponseHelper::jsonResponse(['message' => "Route $uri not found."], 404);
        }
        
        call_user_func($handler);
    }
}