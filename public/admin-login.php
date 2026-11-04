<?php
/** GET /admin-login.php — form. POST — authenticate, redirect to admin area. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: admin-users.php');
    exit;
}

$error = null;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $admin = $username !== '' ? findAdminByUsername($pdo, $username) : false;

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['username'];
        logAdminActivity($pdo, $admin['admin_id'], 'login', 'admin', $admin['admin_id']);
        header('Location: admin-users.php');
        exit;
    } else {
        $error = 'Incorrect username or password.';
    }
}

require TEMPLATES_PATH . '/admin-login.php';
