<?php
require_once '../src/Router.php';
require_once '../src/TicketController.php';

$router = new Router();

$router->addRoute('POST', '/api/ticket', [TicketsController::class, 'create']);
$router->addRoute('DELETE', '/api/ticket', [TicketsController::class, 'delete']);

return $router;
