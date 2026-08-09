<?php
/** GET /admin-modules.php — guarded. List every module with its question count. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';

$error = null;
$modules = [];
try {
    $modules = allModulesWithCounts($pdo);
} catch (PDOException $e) {
    $error = 'Sorry — we couldn’t load the modules just now. Please try again shortly.';
}

$title = 'Modules';
$flash = $_GET['saved'] ?? null;
ob_start();
require TEMPLATES_PATH . '/admin-modules-list.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
