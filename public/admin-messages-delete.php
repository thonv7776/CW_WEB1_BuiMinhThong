<?php
/** POST /admin-messages-delete.php — guarded. No FK on messages. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-messages.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    deleteMessage($pdo, $id);
    logAdminActivity($pdo, $_SESSION['admin_id'], 'delete', 'message', $id);
} catch (PDOException $e) {
    // Fall through to the list rather than showing a stack trace.
}

header('Location: admin-messages.php');
exit;
