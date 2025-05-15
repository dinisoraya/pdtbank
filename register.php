<?php
use App\Models\User;

require_once __DIR__ . '/init.php';

$db = (new Database())->getConnection();
$userModel = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'];

    if (strlen($username) > 20) {
        setFlash('Username must be 20 characters or less.', 'error');
        header('Location: ' . BASE_URL . '/register.php');
        exit();
    }

    if (strlen($password) > 32) {
        setFlash('Password must be 32 characters or less.', 'error');
        header('Location: ' . BASE_URL . '/register.php');
        exit();
    }

    try {
        $userModel->register($username, $password);
        setFlash('Registration successful! You can now login.', 'success');
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    } catch (Exception $e) {
        setFlash($e->getMessage(), 'error');
        header('Location: ' . BASE_URL . '/register.php');
        exit();
    }
}

ob_start();
$title = "Register";
$description = "Please create an account by entering your username and password";
?>

<form action="<?= BASE_URL ?>/register.php" method="POST" class="p-3">
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
            <i class="bi bi-person-plus-fill me-1"></i> Register
        </button>
    </div>

    <div class="text-center">
        <p class="mb-0">Already have an account?
            <a href="<?= BASE_URL ?>/login.php" class="text-decoration-none">Login here</a>
        </p>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/Views/layout/main.php';
?>