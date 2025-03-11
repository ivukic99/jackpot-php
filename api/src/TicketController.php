<?php
require_once 'Ticket.php';

header('Content-Type: application/json');

class TicketsController
{
    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        if ($uri === '/api/ticket' && $method === 'POST') {
            $ticket = new Ticket();
            $ticket->create();
        } elseif ($uri === '/api/ticket' && $method === 'DELETE') {
            // elseif (preg_match('/^\/api\/ticket\/:([0-9]+)$/', $uri, $matches) && $method === 'DELETE')

            $ticket = new Ticket();
            $ticket->delete();
        } else {
            http_response_code(404);
            echo json_encode(['message' => $uri]);
            exit;
        }
    }
}

$controller = new TicketsController();
$controller->handleRequest();
?>