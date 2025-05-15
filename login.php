<?php
use App\Models\User;

require_once __DIR__ . '/init.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $db = (new Database())->getConnection();
    $userModel = new User($db);

    if ($user = $userModel->verifyLogin($username, $password)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        setFlash('Login successful!', 'success');
        header('Location: ' . BASE_URL . '/home.php');
        exit();
    } else {
        setFlash('Invalid username or password', 'error');
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}

ob_start();
$title = "Login";
$description = "Please enter your username and password to login";
?>

<form action="" method="POST" class="p-3">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-pink shadow-sm rounded-pill">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>
    </div>

    <div class="text-center">
        <p class="mb-0">Don't have an account yet?
            <a href="<?= BASE_URL ?>/register.php" class="text-decoration-none">Register here</a>
        </p>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>