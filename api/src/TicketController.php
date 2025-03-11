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
            $ticket = Ticket::create();
            echo json_encode($ticket);
        } elseif (preg_match('/^\/api\/ticket\/:([0-9]+)$/', $uri, $matches)  && $method === 'DELETE') {
            $id = $matches[1];
            if ($id) {
                $response = Ticket::delete($id);
                echo json_encode($response);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Ticket not found.']);
                exit;
            }
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