<?php
/** GET /admin-users-edit.php?id= — guarded, form. POST — validate, update, redirect. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

$id = (int) ($_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['id'] ?? 0) : ($_GET['id'] ?? 0));
$user = getUser($pdo, $id);

if (!$user) {
    $title = 'Page not found';
    ob_start();
    require TEMPLATES_PATH . '/404.php';
    $output = ob_get_clean();
    require TEMPLATES_PATH . '/admin-layout.php';
    exit;
}

$mode = 'edit';
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
            updateUser($pdo, $id, $username, $email);
            logAdminActivity($pdo, $_SESSION['admin_id'], 'update', 'user', $id);
            header('Location: admin-users.php?saved=updated');
            exit;
        } catch (PDOException $e) {
            if (isConstraintViolation($e)) {
                if (stripos($e->getMessage(), 'username') !== false) {
                    $errors['username'] = 'That username is already taken.';
                } else {
                    $errors['email'] = 'That email is already registered.';
                }
            } else {
                $errors['general'] = 'Sorry — the user could not be saved. Please try again.';
            }
        }
    }
}

$title = 'Edit user';
ob_start();
require TEMPLATES_PATH . '/admin-users-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
