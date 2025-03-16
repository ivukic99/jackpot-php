<?php
require_once 'Ticket.php';

class TicketsController
{
    public static function create(): void
    {
        $ticket = new Ticket();
        $ticket->create();
    }

    public static function delete(): void
    {
        $ticket = new Ticket();
        $ticket->delete();
    }
}
