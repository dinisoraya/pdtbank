<?php
require_once __DIR__ . '/init.php';

session_unset();
session_destroy();

setFlash('You have been logged out.', 'success');

header('Location: ' . BASE_URL . '/login.php');
exit();
