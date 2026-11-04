<?php
/** GET /questions.php — list every question. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';

$error = null;
$questions = [];

try {
    $questions = allQuestions($pdo);
} catch (PDOException $e) {
    $error = 'Sorry — we couldn’t load the questions just now. Please try again shortly.';
}

$title = 'Questions';
ob_start();
require TEMPLATES_PATH . '/questions-list.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
