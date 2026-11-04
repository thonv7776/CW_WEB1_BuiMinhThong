<?php
/** GET /admin-modules-edit.php?id= — guarded, form. POST — validate, update, redirect. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-admin.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

$id = (int) ($_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['id'] ?? 0) : ($_GET['id'] ?? 0));
$module = getModule($pdo, $id);

if (!$module) {
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
    $name = trim($_POST['module_name'] ?? '');

    if ($name === '') {
        $errors['module_name'] = 'Please enter a module name.';
    } elseif (mb_strlen($name) > 100) {
        $errors['module_name'] = 'Module name must be 100 characters or fewer.';
    }

    if (!$errors) {
        try {
            updateModule($pdo, $id, $name);
            logAdminActivity($pdo, $_SESSION['admin_id'], 'update', 'module', $id);
            header('Location: admin-modules.php?saved=updated');
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

$title = 'Edit module';
ob_start();
require TEMPLATES_PATH . '/admin-modules-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/admin-layout.php';
