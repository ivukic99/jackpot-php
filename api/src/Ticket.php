<?php
require_once 'Database.php';
require_once 'RequestValidator.php';
require_once 'ResponseHelper.php';
require_once 'SocketClient.php';
require_once 'RabbitMQSender.php';
require_once 'ErrorLogger.php';

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(): void
    {
        try {
            $request = json_decode(file_get_contents("php://input"), true);

            $rules = [
                'amount' => ['required', 'number', 'positive'],
            ];

            $validator = new RequestValidator($this->db);
            if (!$validator->validate($request, $rules)) {
                ResponseHelper::jsonResponse(['error' => $validator->getErrors()], 400);
            }

            $amount = $request['amount'];
            $jackpot_fee = $this->calculate_jackpot_fee($amount);

            $this->db->beginTransaction();

            $ticket_id = $this->insertTicket($amount, $jackpot_fee);

            $this->updateJackpot($jackpot_fee);

            $jackpot = $this->getJackpotTotal();

            $this->db->commit();

            $data = [
                "total" => $jackpot['total']
            ];
            $client = new SocketClient();
            $client->sendData(json_encode($data));

            ResponseHelper::jsonResponse([
                'message' => 'Success.',
                'data' => [
                    'ticket_id' => $ticket_id
                ]
            ], 201);
        } catch (Exception $e) {
            $this->db->rollBack();
            ErrorLogger::error($e->getMessage(), __FILE__, __LINE__);
            ResponseHelper::jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function delete(): void
    {
        try {
            $request = json_decode(file_get_contents("php://input"), true);

            $rules = [
                'ticket_id' => ['required', 'int', 'positive', 'exists:tickets,id'],
            ];

            $validator = new RequestValidator($this->db);
            if (!$validator->validate($request, $rules)) {
                ResponseHelper::jsonResponse(['error' => $validator->getErrors()], 400);
            }

            $ticket_id = $request['ticket_id'];

            $ticket = $this->getTicketById($ticket_id);

            $this->db->beginTransaction();

            $this->deleteTicketById($ticket_id);

            $this->updateJackpot(-$ticket['jackpot_fee']);

            $jackpot = $this->getJackpotTotal();

            $this->db->commit();

            $data = [
                "total" => $jackpot['total']
            ];
            $client = new SocketClient();
            $client->sendData(json_encode($data));

            ResponseHelper::jsonResponse([
                'message' => 'Success.',
            ]);
        } catch (Exception $e) {
            $this->db->rollBack();
            ErrorLogger::error($e->getMessage(), __FILE__, __LINE__);
            ResponseHelper::jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function insertTicket($amount, $jackpot_fee): string
    {
        $query = "INSERT INTO tickets (amount, jackpot_fee) VALUES (:amount, :jackpot_fee)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'amount' => $amount,
            'jackpot_fee' => $jackpot_fee
        ]);
        return $this->db->lastInsertId();
    }

    private function updateJackpot($jackpot_fee): void
    {
        $query = "UPDATE jackpot SET total = total + :fee WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['fee' => $jackpot_fee]);
    }

    private function getJackpotTotal()
    {
        $query = "SELECT total FROM jackpot WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getTicketById($ticket_id)
    {
        $query = "SELECT * FROM tickets WHERE id = :ticket_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['ticket_id' => $ticket_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function deleteTicketById($ticket_id): void
    {
        $query = "DELETE FROM tickets WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $ticket_id]);
    }

    private function calculate_jackpot_fee(float $amount): float
    {
        return $amount * 0.03;
    }
}

