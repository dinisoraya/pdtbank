<?php
require_once __DIR__ . '/init.php';

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

header('Location: login.php');
exit();
