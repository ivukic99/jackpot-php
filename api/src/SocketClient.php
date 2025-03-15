<?php
require_once 'RabbitMQSender.php';
class SocketClient {
    private $host;
    private $port;

    public function __construct($host = 'websocket', $port = 4000) {
        $this->host = $host;
        $this->port = $port;
    }

    public function sendData($data): void
    {
        $socket = @stream_socket_client("{$this->host}:{$this->port}",$errno, $errstr, 2);

        if (!$socket) {
            $rabbitmq = new RabbitMQSender();
            $rabbitmq->sender($data);
        } else {
            fwrite($socket, $data);
            fclose($socket);
        }
    }
}