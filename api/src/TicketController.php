<?php
require_once 'Ticket.php';
require_once 'ResponseHelper.php';
require_once 'Middleware.php';

header('Content-Type: application/json');

class TicketsController
{
    public static function handleRequest(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        if ($uri === '/api/ticket' && $method === 'POST') {
            $ticket = new Ticket();
            $ticket->create();
        } elseif ($uri === '/api/ticket' && $method === 'DELETE') {
            $ticket = new Ticket();
            $ticket->delete();
        } else {
            ResponseHelper::jsonResponse(['message' => $uri], 404);
        }
    }
}

Middleware::apiKeyAuthenticate();
Middleware::ensureJsonRequest();
TicketsController::handleRequest();
