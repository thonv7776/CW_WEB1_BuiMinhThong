<?php
/** GET /admin-messages.php — guarded. Inbox of contact-form submissions. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';

$error = null;
$messages = [];
try {
    $messages = allMessages($pdo);
} catch (PDOException $e) {
    $error = 'Sorry — we couldn’t load the messages just now. Please try again shortly.';
}

$title = 'Messages';
ob_start();
require TEMPLATES_PATH . '/admin-messages-list.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
