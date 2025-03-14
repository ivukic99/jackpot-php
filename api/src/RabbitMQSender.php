<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
class RabbitMQSender {
    private $connection;
    private $channel;

    public function __construct() {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
        $this->channel = $this->connection->channel();

        $args = new AMQPTable();
        $args->set('x-max-length', 1);
        $this->channel->queue_declare('jackpot_queue', false, true, false, false, false, $args);
    }

    public function sender($data) {
        $msg = new AMQPMessage($data, ['delivery_mode' => 2]);
        $this->channel->basic_publish($msg, '', 'jackpot_queue');
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}