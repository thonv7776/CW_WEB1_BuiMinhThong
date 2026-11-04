<?php
/** POST /admin-users-delete.php — guarded. Blocked by FK RESTRICT if the user still has questions. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-users.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    deleteUser($pdo, $id);
    logAdminActivity($pdo, $_SESSION['admin_id'], 'delete', 'user', $id);
    header('Location: admin-users.php?saved=deleted');
} catch (PDOException $e) {
    header('Location: admin-users.php?saved=' . (isConstraintViolation($e) ? 'inuse' : 'error'));
}
exit;
