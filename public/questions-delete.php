<?php
/**
 * POST /questions-delete.php — delete a question. Owner or admin only.
 */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: questions.php');
    exit;
}

if (empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $question = getQuestion($pdo, $id);
    if ($question) {
        $isAdmin = !empty($_SESSION['admin_id']);
        $isOwner = !empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $question['user_id'];

        if ($isAdmin || $isOwner) {
            deleteQuestion($pdo, $id);
            if (!empty($question['image']) && !in_array($question['image'], ['pdo-empty-result.png', 'flexbox-center.png'], true)) {
                deleteQuestionImage($question['image']);
            }
            if ($isAdmin) {
                logAdminActivity($pdo, $_SESSION['admin_id'], 'delete', 'question', $id);
            }
        }
    }
} catch (PDOException $e) {
    // Fall through to the list rather than showing a stack trace.
}

header('Location: questions.php');
exit;
