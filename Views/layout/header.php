<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>pdtbank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo.png" type="image/png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-yellow shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/login.php">
                <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo" class="logo me-2">
                <span class="fw-bold">pdtbank</span>
            </a>
            <?php if (!empty($_SESSION['username'])): ?>
                <button class="navbar-toggler" type="button" onclick="toggleNav()" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link fw-bold">👋 Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i>
                                Logout</a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
            </ul>
        </div>
        </div>
    </nav>

    <script>
        function toggleNav() {
            document.getElementById('navbarContent').classList.toggle('show');
        }
    </script>