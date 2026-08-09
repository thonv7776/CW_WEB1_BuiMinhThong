<?php
/** GET /contact.php — form. POST — validate, insert, redirect with a flash. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';

$errors = [];
$old = [];
$sent = isset($_GET['sent']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');

    if (isBlank($name)) {
        $errors['name'] = 'Please enter your name.';
    }
    if (isBlank($email)) {
        $errors['email'] = 'Please enter your email.';
    } elseif (!validEmail($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (isBlank($subject)) {
        $errors['subject'] = 'Please enter a subject.';
    }
    if (isBlank($body)) {
        $errors['body'] = 'Please enter a message.';
    }

    if (!$errors) {
        try {
            insertMessage($pdo, $name, $email, $subject, $body);
            header('Location: contact.php?sent=1');
            exit;
        } catch (PDOException $e) {
            $errors['general'] = 'Sorry — your message could not be sent. Please try again.';
        }
    }
}

$title = 'Contact';
ob_start();
require TEMPLATES_PATH . '/contact-form.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
