<?php
use App\Models\Account;

require_once __DIR__ . '/init.php';

ensureAuthenticated();

$db = (new Database())->getConnection();
$accountModel = new Account($db);

$userId = $_SESSION['user_id'];
$account = $accountModel->findByUserId($userId);
$balance = $account['balance'] ?? 0;

ob_start();
$title = "pdtbank";
$description = "Smart banking made simple. No drama, just your data.";
?>

<div class="mt-4 px-3">
    <div class="mb-3 text-center">
        <h6 class="text-muted mb-1">Account</h6>
        <div class="fw-bold fs-5">
            <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?> -
            <?= htmlspecialchars($account['account_number'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center bg-light rounded-3 p-3 shadow-sm">
        <div class="d-flex align-items-center me-2">
            <span class="me-1 text-secondary">Balance:</span>
            <span class="fw-semibold fs-5 ms-1">Rp</span>
            <span id="balance" class="fs-4 fw-semibold ms-1"
                data-real-balance="<?= number_format($balance, 2, ',', '.') ?>">••••••••</span>
        </div>
        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center" onclick="toggleBalance()">
            <i id="eye-icon" class="bi bi-eye"></i>
        </button>
    </div>

    <h5 class="text-center mt-5 mb-4 fw-bold text-pink">Menu</h5>

    <div class="d-flex justify-content-center flex-wrap gap-3 px-3">
        <a href="<?= BASE_URL ?>/deposit.php"
            class="btn btn-pink px-4 py-2 d-flex align-items-center shadow-sm rounded-pill">
            <i class="bi bi-piggy-bank me-2"></i> Deposit
        </a>
        <a href="<?= BASE_URL ?>/transfer.php"
            class="btn btn-pink px-4 py-2 d-flex align-items-center shadow-sm rounded-pill">
            <i class="bi bi-arrow-repeat me-2"></i> Transfer
        </a>
        <a href="<?= BASE_URL ?>/history.php"
            class="btn btn-pink px-4 py-2 d-flex align-items-center shadow-sm rounded-pill">
            <i class="bi bi-journal-text me-2"></i> History
        </a>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/balance.js"></script>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>