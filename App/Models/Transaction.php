<?php
namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Transaction
{
    private PDO $conn;
    private Account $accountModel;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
        $this->accountModel = new Account($db);
    }

    public function transfer(int $fromUserId, string $toAccountNumber, float $amount): void
    {
        $fromAccount = $this->accountModel->findByUserId($fromUserId);
        if (!$fromAccount) {
            throw new Exception("Sender account not found.");
        }

        $txId = $this->uuid4();

        try {
            // Start a transaction
            // This is important to ensure that the transfer is atomic
            $this->conn->beginTransaction();
            // Call the transfer_money stored procedure
            $stmt = $this->conn->prepare("CALL transfer_money(?, ?, ?, ?)");
            $stmt->execute([
                $txId,
                $fromAccount['account_number'],
                $toAccountNumber,
                $amount
            ]);

            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $errorInfo = $e->errorInfo ?? [];
            $message = $errorInfo[2] ?? $e->getMessage();

            throw new Exception("Transfer failed: SQLSTATE[{$errorInfo[0]}]: {$errorInfo[1]} {$message}");
        }
    }

    public function deposit(int $userId, float $amount): void
    {
        $toAccount = $this->accountModel->findByUserId($userId);
        if (!$toAccount) {
            throw new Exception("Recipient account not found.");
        }

        $txId = $this->uuid4();

        try {
            // Start a transaction
            // This is important to ensure that the deposit is atomic
            $this->conn->beginTransaction();
            // Call the deposit_money stored procedure
            $stmt = $this->conn->prepare("CALL deposit_money(?, ?, ?)");
            $stmt->execute([
                $txId,
                $toAccount['account_number'],
                $amount
            ]);

            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $errorInfo = $e->errorInfo ?? [];
            $message = $errorInfo[2] ?? $e->getMessage();

            throw new Exception("Deposit failed: SQLSTATE[{$errorInfo[0]}]: {$errorInfo[1]} {$message}");
        }
    }

    public function getTransactionHistory(int $userId): array
    {
        $account = $this->accountModel->findByUserId($userId);
        if (!$account) {
            throw new Exception("Account not found.");
        }
        $accountNumber = $account['account_number'];

        // Call the get_transaction_history stored procedure
        $stmt = $this->conn->prepare("CALL get_transaction_history(?)");
        $stmt->execute([$accountNumber]);
        $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate cumulative balance (balance_at_that_time)
        $balance = 0;
        foreach ($raw as &$row) {
            $balance += $row['net_change'];
            $row['balance_at_that_time'] = $balance;
        }
        return $raw;
    }

    private function uuid4(): string
    {
        // Generate a random UUID (version 4)
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
