<?php
require_once  'ResponseHelper.php';
class SocketClient {
    private $host;
    private $port;

    public function __construct($host = 'websocket', $port = 4000) {
        $this->host = $host;
        $this->port = $port;
    }

    public function sendData($data) {
        $socket = stream_socket_client("{$this->host}:{$this->port}",$errno, $errstr, 30);

        if (!$socket) {
            ResponseHelper::jsonResponse(['error' => "Problem with socket connection."], 400);
        }

        fwrite($socket, $data);
        fclose($socket);
    }
}