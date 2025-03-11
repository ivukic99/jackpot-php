<?php
    require_once 'Database.php';

    class Ticket {
        public static function create() {
            $db = Database::connect();
            $amount = floatval($_POST['amount']);

            $sql = "INSERT INTO tickets (amount) VALUES (:amount)";

            $stmt = $db->prepare($sql);

            if ($stmt->execute(['amount' => $amount])) {
                $crated_row = $db->lastInsertId();
                echo json_encode(['ticket_id' => $crated_row]);
                exit;
            } else {
                die('Failed to insert ticket.');
            }
        }

        public static function delete($id) {
            $db = Database::connect();
            $sql = "DELETE FROM tickets WHERE id = :id";
            $stmt = $db->prepare($sql);
            if ($stmt->execute(['id' => $id])) {
                return ['message' => 'Success'];
            } else {
                die('Failed to delete ticket.');
            }

        }
    }
?>
