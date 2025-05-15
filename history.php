<?php
require_once __DIR__ . '/init.php';

ensureAuthenticated();

$db = (new Database())->getConnection();
$transactionModel = new \App\Models\Transaction($db);
$transactions = $transactionModel->getTransactionHistory($_SESSION['user_id']);

if ($transactions === false) {
    setFlash('Unable to fetch transaction history.', 'error');
}

ob_start();
$title = "Transaction History";
$description = "View your recent transaction history";
?>

<div class="text-end">
    <a href="<?= BASE_URL ?>/home.php" class="btn btn-pink px-4 py-2 shadow-sm rounded-pill m-2">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped mt-4">
        <thead class="align-middle">
            <tr>
                <th>No</th>
                <th>Date & Time</th>
                <th class="table-description">Description</th>
                <th>Amount (Rp)</th>
                <th>Balance (Rp)</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $index => $transaction): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= htmlspecialchars($transaction['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="table-description">
                            <?= htmlspecialchars($transaction['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td style="color: <?= $transaction['net_change'] < 0 ? 'red' : 'green' ?>;">
                            <?= $transaction['net_change'] < 0
                                ? number_format($transaction['net_change'], 2)
                                : '+' . number_format($transaction['net_change'], 2)
                                ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(number_format($transaction['balance_at_that_time'], 2), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No transactions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>