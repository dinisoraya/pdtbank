<?php
namespace App\Models;

use PDO;
use Exception;

class User
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    private function generateUniqueAccountNumber(): string
    {
        do {
            // Generate a random 10-digit account_number
            $number = strval(rand(1000000000, 9999999999));
            $stmt = $this->conn->prepare("SELECT 1 FROM accounts WHERE account_number = ?");
            $stmt->execute([$number]);
        } while ($stmt->fetchColumn());
        return $number;
    }

    public function register(string $username, string $password): bool
    {
        if ($this->findByUsername($username)) {
            throw new Exception("Username already exists.");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Start a transaction
            // This is important to ensure that the registration is atomic
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);
            $userId = $this->conn->lastInsertId();

            $accountNumber = $this->generateUniqueAccountNumber();
            $stmtAcc = $this->conn->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, 0)");
            $stmtAcc->execute([$userId, $accountNumber]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Registration failed due to database error.");
        }
    }

    public function verifyLogin(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
