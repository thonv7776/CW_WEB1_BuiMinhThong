<?php
/** Log out and return to the public home page. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';

if (!empty($_SESSION['admin_id'])) {
    logAdminActivity($pdo, $_SESSION['admin_id'], 'logout', 'admin', $_SESSION['admin_id']);
}

unset($_SESSION['admin_id'], $_SESSION['admin_name']);

header('Location: index.php');
exit;
