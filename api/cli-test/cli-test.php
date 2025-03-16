<?php

require '../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$client = new Client();

function sendRequest($method, $url, $data = [])
{
    global $client;
    $config = require_once __DIR__ . '/../config/config.php';

    $options = [
        'json' => $data,
        'headers' => [
            'Content-Type' => 'application/json',
            'X-API-Key' => $config['API_KEY']
        ],
    ];

    try {
        $response = $client->request($method, $url, $options);
        return [
            'status' => $response->getStatusCode(),
            'response' => json_decode($response->getBody(), true),
        ];
    } catch (Exception $e) {
        return [
            'status' => $e->getCode(),
            'response' => $e->getMessage(),
        ];
    }
}

function parseArguments($argv)
{
    $args = [];
    foreach ($argv as $arg) {
        if (preg_match('/--(\w+)=(.*)/', $arg, $match)) {
            $args[$match[1]] = $match[2];
        }
    }
    return $args;
}

$args = parseArguments($argv);

if (!isset($args['method'])) {
    echo "Usage: php cli-test.php --method=POST|DELETE [--ticket_id=1 --amount=50]\n";
    exit(1);
}

$method = strtoupper($args['method']);
$ticketId = isset($args['ticket_id']) ? (int)$args['ticket_id'] : null;
$amount = isset($args['amount']) ? (int)$args['amount'] : null;

$apiUrl = 'http://localhost:8080/api/ticket';

$data = [
    'ticket_id' => $ticketId,
    'amount' => $amount
];

$result = sendRequest($method, $apiUrl, $data);

print_r($result);

