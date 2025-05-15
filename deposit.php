<?php
require_once __DIR__ . '/init.php';

ensureAuthenticated();

$db = (new Database())->getConnection();
$accountModel = new \App\Models\Account($db);
$transactionModel = new \App\Models\Transaction($db);
$account = $accountModel->findByUserId($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

    try {
        $transactionModel->deposit($_SESSION['user_id'], $amount);
        $formatted = number_format($amount, 2, ',', '.');
        $message = sprintf('Successfully deposited Rp%s to your account!', $formatted);
        setFlash($message, 'success');
        header('Location: ' . BASE_URL . '/deposit.php');
        exit();
    } catch (Exception $e) {
        setFlash($e->getMessage(), 'error');
        header('Location: ' . BASE_URL . '/deposit.php');
        exit();
    }
}

ob_start();
$title = "Cash Deposit Machine";
$description = "Deposit to your account";
?>

<form method="POST" action="<?= BASE_URL ?>/deposit.php">
    <div class="mb-3">
        <label class="form-label">Account</label>
        <input type="text" class="form-control"
            value="<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($account['account_number'], ENT_QUOTES, 'UTF-8') ?>"
            disabled>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" class="form-control" required>
    </div>
    <div class="text-center">
        <a href="<?= BASE_URL ?>/home.php" class="btn btn-pink px-4 py-2 shadow-sm rounded-pill m-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-pink px-4 py-2 shadow-sm rounded-pill m-2">
            Deposit
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>