<?php
namespace App\Models;

use PDO;

class Account
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM accounts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByAccountNumber(string $accountNumber): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT a.*, u.username
            FROM accounts a
            JOIN users u ON a.user_id = u.id
            WHERE a.account_number = ?
        ");
        $stmt->execute([$accountNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getBalance(int $userId): float
    {
        // Read the balance using a get_balance function
        $account = $this->findByUserId($userId);
        if (!$account) {
            return 0.0;
        }
        $accountNumber = $account['account_number'];
        $stmt = $this->conn->prepare("SELECT get_balance(?) AS balance");
        $stmt->execute([$accountNumber]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['balance'] : 0.0;
    }
}
