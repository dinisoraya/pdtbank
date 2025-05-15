<?php include __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 col-lg-8 mx-auto text-start">
            <div class="card shadow p-4 p-md-5">
                <?php
                $successMessage = getFlash('success');
                $errorMessage = getFlash('error');
                ?>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php elseif ($successMessage): ?>
                    <div class="alert alert-success">
                        <?= $successMessage ?>
                    </div>
                <?php endif; ?>

                <h5 class="fw-bold mb-3"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
                <p><?= htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <hr class="my-3">
                <?= $content ?? ''; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>