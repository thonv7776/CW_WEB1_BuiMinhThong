<?php
/** POST /admin-modules-delete.php — guarded. Blocked by FK RESTRICT if in use. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-modules.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    deleteModule($pdo, $id);
    logAdminActivity($pdo, $_SESSION['admin_id'], 'delete', 'module', $id);
    header('Location: admin-modules.php?saved=deleted');
} catch (PDOException $e) {
    header('Location: admin-modules.php?saved=' . (isConstraintViolation($e) ? 'inuse' : 'error'));
}
exit;
