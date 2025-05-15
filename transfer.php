<?php
require_once __DIR__ . '/init.php';

ensureAuthenticated();

$db = (new Database())->getConnection();
$accountModel = new \App\Models\Account($db);
$transactionModel = new \App\Models\Transaction($db);
$account = $accountModel->findByUserId($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toAccountNumber = trim($_POST['to_account']);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

    try {
        $transactionModel->transfer($_SESSION['user_id'], $toAccountNumber, $amount);

        $toAccount = $accountModel->findByAccountNumber($toAccountNumber);
        $toUsername = $toAccount ? $toAccount['username'] : 'Unknown';
        $formatted = number_format($amount, 2, ',', '.');

        setFlash("Successfully transferred Rp{$formatted} to {$toUsername} – {$toAccountNumber}!", 'success');
        header('Location: ' . BASE_URL . '/transfer.php');
        exit();
    } catch (Exception $e) {
        setFlash($e->getMessage(), 'error');
        header('Location: ' . BASE_URL . '/transfer.php');
        exit();
    }
}

ob_start();
$title = "Rupiah Transfer";
$description = "Transfer money to another account";
?>

<form action="<?= BASE_URL ?>/transfer.php" method="POST">
    <div class="mb-3">
        <label class="form-label">From Account</label>
        <input type="text" class="form-control"
            value="<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($account['account_number'], ENT_QUOTES, 'UTF-8') ?>"
            disabled>
    </div>

    <div class="mb-3">
        <label for="to_account" class="form-label">To Account Number</label>
        <input type="text" name="to_account" id="to_account" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" class="form-control" required>
    </div>

    <div class="text-center">
        <a href="<?= BASE_URL ?>/home.php" class="btn btn-pink px-4 py-2 shadow-sm rounded-pill m-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-pink px-4 py-2 shadow-sm rounded-pill m-2">
            Transfer
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>