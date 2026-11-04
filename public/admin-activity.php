<?php
/** GET /admin-activity.php — guarded. Recent admin activity (audit trail). */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';

$error = null;
$entries = [];
try {
    //activity log
    $entries = allActivityLog($pdo, 100);
} catch (PDOException $e) {
    $error = 'Sorry — we couldn’t load the activity log just now. Please try again shortly.';
}

$title = 'Activity log';
ob_start();
require TEMPLATES_PATH . '/admin-activity.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
