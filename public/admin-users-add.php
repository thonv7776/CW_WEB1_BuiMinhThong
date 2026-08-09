<?php
/** GET /admin-users-add.php — guarded, form. POST — validate, insert, redirect. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

$mode = 'add';
$user = null;
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username === '') {
        $errors['username'] = 'Please enter a username.';
    } elseif (mb_strlen($username) > 50) {
        $errors['username'] = 'Username must be 50 characters or fewer.';
    }
    if (isBlank($email)) {
        $errors['email'] = 'Please enter an email address.';
    } elseif (!validEmail($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (!$errors) {
        try {
            $newId = insertUser($pdo, $username, $email);
            logAdminActivity($pdo, $_SESSION['admin_id'], 'create', 'user', $newId);
            header('Location: admin-users.php?saved=created');
            exit;
        } catch (PDOException $e) {
            if (isConstraintViolation($e)) {
                $errors['email'] = 'That email is already registered.';
            } else {
                $errors['general'] = 'Sorry — the user could not be saved. Please try again.';
            }
        }
    }
}

$title = 'New user';
ob_start();
require TEMPLATES_PATH . '/admin-users-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
