<?php
/** GET /admin-users.php — guarded. List every user with their question count. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';

$error = null;
$users = [];
try {
    $users = allUsersWithCounts($pdo);
} catch (PDOException $e) {
    $error = 'Sorry — we couldn’t load the users just now. Please try again shortly.';
}

$title = 'Users';
$flash = $_GET['saved'] ?? null;
ob_start();
require TEMPLATES_PATH . '/admin-users-list.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
