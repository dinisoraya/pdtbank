<?php
require_once __DIR__ . '/init.php';

$db = (new Database())->getConnection();
$userModel = new App\Models\User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $user = $userModel->verifyLogin($username, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: home.php');
        exit();
    } else {
        setFlash('Invalid username or password', 'error');
        header('Location: login.php');
        exit();
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
} else {
    header('Location: login.php');
}
exit();
