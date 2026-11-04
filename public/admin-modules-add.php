<?php
/** GET /admin-modules-add.php — guarded, form. POST — validate, insert, redirect. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

$mode = 'add';
$module = null;
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $name = trim($_POST['module_name'] ?? '');

    if ($name === '') {
        $errors['module_name'] = 'Please enter a module name.';
    } elseif (mb_strlen($name) > 100) {
        $errors['module_name'] = 'Module name must be 100 characters or fewer.';
    }

    if (!$errors) {
        try {
            $newId = insertModule($pdo, $name);
            logAdminActivity($pdo, $_SESSION['admin_id'], 'create', 'module', $newId);
            header('Location: admin-modules.php?saved=created');
            exit;
        } catch (PDOException $e) {
            if (isConstraintViolation($e)) {
                $errors['module_name'] = 'A module named "' . $name . '" already exists.';
            } else {
                $errors['general'] = 'Sorry — the module could not be saved. Please try again.';
            }
        }
    }
}

$title = 'New module';
ob_start();
require TEMPLATES_PATH . '/admin-modules-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
