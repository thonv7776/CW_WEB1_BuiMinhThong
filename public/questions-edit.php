<?php

require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/upload.php';


//check user or admin exist ?
if (empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['id'] ?? 0) : ($_GET['id'] ?? 0));
$question = getQuestion($pdo, $id);

if (!$question) {
    $title = 'Page not found';
    ob_start();
    require TEMPLATES_PATH . '/404.php';
    $output = ob_get_clean();
    require TEMPLATES_PATH . '/layout.php';
    exit;
}

$isAdminEditing = !empty($_SESSION['admin_id']);
$isOwner = !empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $question['user_id'];

if (!$isAdminEditing && !$isOwner) {
    $title = 'Not allowed';
    ob_start();
    require TEMPLATES_PATH . '/403.php';
    $output = ob_get_clean();
    require TEMPLATES_PATH . '/layout.php';
    exit;
}

$mode = 'edit';
$errors = [];
$old = [];

$modules = [];
$users = [];
$ownerUsername = '';
try {
    $modules = allModules($pdo);
    if ($isAdminEditing) {
        $users = allUsers($pdo);
    } else {
        $owner = getUser($pdo, $question['user_id']);
        $ownerUsername = $owner ? $owner['username'] : '';
    }
} catch (PDOException $e) {
    $errors['general'] = 'Could not load the form options. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $qTitle = trim($_POST['title'] ?? '');
    $qContent = trim($_POST['content'] ?? '');
    $moduleId = (int) ($_POST['module_id'] ?? 0);
    $userId = $isAdminEditing ? (int) ($_POST['user_id'] ?? 0) : (int) $question['user_id'];

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
    if ($isAdminEditing && $userId <= 0) {
        $errors['user_id'] = 'Please choose an author.';
    }

    
    $imageName = $question['image'];
    $newImage = saveQuestionImage($_FILES['image'] ?? null, $imgErr);
    if ($imgErr !== null) {
        $errors['image'] = $imgErr;
    }

    if ($errors) {
        if ($newImage !== null) {
            deleteQuestionImage($newImage);
        }
    } else {
        if ($newImage !== null) {
            $imageName = $newImage;
        }

        try {
            updateQuestion($pdo, $id, $qTitle, $qContent, $imageName, $userId, $moduleId);

            if ($newImage !== null && !empty($question['image']) && $question['image'] !== $newImage) {
                deleteQuestionImage($question['image']);
            }
            if ($isAdminEditing) {
                logAdminActivity($pdo, $_SESSION['admin_id'], 'update', 'question', $id);
            }

            header('Location: questions.php');
            exit;
        } catch (PDOException $e) {
            if ($newImage !== null) {
                deleteQuestionImage($newImage);
            }
            $errors['general'] = 'Sorry — the changes could not be saved. Please try again.';
        }
    }
}

$title = 'Edit question';
ob_start();
require TEMPLATES_PATH . '/questions-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
