<?php
/** GET /questions-add.php — form. POST — validate, upload, insert, redirect. Requires login. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/require-login.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/upload.php';

$mode = 'add';
$question = null;
$errors = [];
$old = [];
$sessionUsername = $_SESSION['username'];

$modules = [];
try {
    $modules = allModules($pdo);
} catch (PDOException $e) {
    $errors['general'] = 'Could not load the modules. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $qTitle = trim($_POST['title'] ?? '');
    $qContent = trim($_POST['content'] ?? '');
    $moduleId = (int) ($_POST['module_id'] ?? 0);
    $userId = (int) $_SESSION['user_id'];

    if ($qTitle === '') {
        $errors['title'] = 'Please enter a title.';
    } elseif (mb_strlen($qTitle) > 150) {
        $errors['title'] = 'Title must be 150 characters or fewer.';
    }
    if ($qContent === '') {
        $errors['content'] = 'Please describe your question.';
    }
    if ($moduleId <= 0) {
        $errors['module_id'] = 'Please choose a module.';
    }

    $imageName = saveQuestionImage($_FILES['image'] ?? null, $imgErr);
    if ($imgErr !== null) {
        $errors['image'] = $imgErr;
    }

    if ($errors) {
        if ($imageName !== null) {
            deleteQuestionImage($imageName);
        }
    } else {
        try {
            insertQuestion($pdo, $qTitle, $qContent, $imageName, $userId, $moduleId);
            header('Location: questions.php');
            exit;
        } catch (PDOException $e) {
            if ($imageName !== null) {
                deleteQuestionImage($imageName);
            }
            $errors['general'] = 'Sorry — the question could not be saved. Please check your choices and try again.';
        }
    }
}

$title = 'Ask a question';
ob_start();
require TEMPLATES_PATH . '/questions-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
