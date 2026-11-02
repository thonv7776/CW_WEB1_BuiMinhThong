<?php

require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: questions.php');
    exit;
}

$error = null;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $user = $identifier !== '' ? findUserForLogin($pdo, $identifier) : false;

    if ($user && $user['password_hash'] !== null && password_verify($password, $user['password_hash'])) {
        if ((int) $user['is_verified'] !== 1) {
            header('Location: verify.php?email=' . urlencode($user['email']));
            exit;
        }

        #session based author tracking
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header('Location: questions.php');
        exit;
    }

    $error = 'Incorrect username/email or password.';
}

$title = 'Log in';
ob_start();
require TEMPLATES_PATH . '/login.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
