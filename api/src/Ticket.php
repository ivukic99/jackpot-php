<?php
    require_once 'Database.php';
    require_once 'RequestValidator.php';
    require_once 'ResponseHelper.php';

    class Ticket {
        private $db;
        public function __construct() {
            $this->db = Database::connect();
        }
        public function create() {
            try {
                $this->db->beginTransaction();

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

                $insert_query = "INSERT INTO tickets (amount, jackpot_fee) VALUES (:amount, :jackpot_fee)";
                $insert_stmt = $this->db->prepare($insert_query);
                $insert_stmt->execute([
                    'amount' => $amount,
                    'jackpot_fee' => $jackpot_fee
                ]);

                $ticket_id = $this->db->lastInsertId();

                $update_query = "UPDATE jackpot SET total = total + :fee WHERE id = 1";
                $update_stmt = $this->db->prepare($update_query);
                $update_stmt->execute(['fee' => $jackpot_fee]);

                $this->db->commit();

                ResponseHelper::jsonResponse([
                    'message' => 'Success.',
                    'data' => [
                        'ticket_id' => $ticket_id
                    ]
                ], 201);
            } catch (Exception $e) {
                $this->db->rollBack();
                ResponseHelper::jsonResponse(['error' => $e->getMessage()], 400);
            }
        }

        public function delete() {
            try {
                $this->db->beginTransaction();
                $request = json_decode(file_get_contents("php://input"), true);

                $rules = [
                    'ticket_id' => ['required', 'int', 'positive', 'exists:tickets,id'],
                ];

                $validator = new RequestValidator($this->db);
                if (!$validator->validate($request, $rules)) {
                    ResponseHelper::jsonResponse(['error' => $validator->getErrors()], 400);
                }

                $ticket_id = $request['ticket_id'];

                $select_query = "SELECT * FROM tickets WHERE id = :ticket_id";
                $select_stmt = $this->db->prepare($select_query);
                $select_stmt->execute(['ticket_id' => $ticket_id]);
                $ticket = $select_stmt->fetch(PDO::FETCH_ASSOC);

                $delete_query = "DELETE FROM tickets WHERE id = :id";
                $delete_stmt = $this->db->prepare($delete_query);
                $delete_stmt->execute(['id' => $ticket_id]);

                $update_query = "UPDATE jackpot SET total = total - :fee WHERE id = 1";
                $update_stmt = $this->db->prepare($update_query);
                $update_stmt->execute(['fee' => $ticket['jackpot_fee']]);

                $this->db->commit();

                ResponseHelper::jsonResponse([
                    'message' => 'Success.',
                ]);
            } catch (Exception $e) {
                $this->db->rollBack();
                ResponseHelper::jsonResponse(['error' => $e->getMessage()], 400);
            }
        }

        private function calculate_jackpot_fee(float $amount) {
            $value = $amount * 0.03;
            return $value;
        }
    }

