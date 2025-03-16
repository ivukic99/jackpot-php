# Jackpot Tracking System

## Overview
The Jackpot Tracking System is a containerized solution for handling ticket transactions and providing real-time jackpot updates. It consists of:

- **PHP API**: API for handling ticket transactions and updating jackpots.
- **Node.js WebSocket Server**: A Socket.io-powered WebSocket server to broadcast real-time jackpot updates.
- **Dockerized Environment**: The entire system runs within Docker containers for easy deployment and scalability.

## Technologies Used
- PHP (without a framework)
- Node.js with Socket.io
- MySQL
- Docker & Docker Compose
- Apache (for PHP API)
- Redis
- RabbitMQ

---

## Installation and Running the System

### Clone the Repository
```bash
git clone https://github.com/ivukic99/jackpot-php.git
cd jackpot-php
```

### Run and Build Docker Containers
```bash
docker compose up -d --build
```

### Running CLI Tests
```bash
cd api
composer install
php cli-test/cli-test.php --method=POST|DELETE [--amount=50 | --ticket_id=1]
```

## API Testing with Postman

### POST / DELETE Ticket Endpoint:
```bash
http://localhost:8080/api/ticket
```

### Headers:
```json
{
  "Content-Type": "application/json",
  "X-API-Key": "BLOzhmBns8eKg1qE81hAOtHlajJlufrLzqsbJaviesaOfKRnEGAf2pF1HjKfmo6p"
}
```

### POST Body Example (Create Ticket):
```json
{
  "amount": 50
}
```

### DELETE Body Example (Delete Ticket by ID):
```json
{
  "ticket_id": 1
}
```

## 🔥 Real-Time Jackpot Update via Postman WebSocket (Socket.io)

### Open WebSocket Connection:
```ws://localhost:3000```

### Event to Listen For:
```jackpot_update```

### Steps to Test:
1. Open a WebSocket client (Postman Socket.io client).
2. Connect to `ws://localhost:3000`.
3. Set an event listener for `jackpot_update`.
4. When the PHP API updates the jackpot, you'll receive the data in real-time without needing to reload the page.

---

## 🛑 Testing Retry Sending Mechanism (API)
To test the retry sending mechanism when the Node.js server is down, follow these steps:
1. In the terminal, run: `docker stop websocket`
2. Test the API by sending a request via Postman or CLI.
3. Restart the Node.js WebSocket server: `docker start websocket`
4. In the Postman WebSocket client, connect to `ws://localhost:3000` again and see updates.
5. You can verify the data consistency by accessing the `jackpot_db` database in phpMyAdmin and checking the `jackpot` table.

### Service URLs:
- MySQL (phpMyAdmin): [http://localhost:8000/](http://localhost:8000/)
- RabbitMQ (username: admin, password: admin): [http://localhost:15672/](http://localhost:15672/)

---

## Contributors
- [Igor Vukic](https://github.com/ivukic99)



