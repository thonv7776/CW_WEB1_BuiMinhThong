<?php
/** GET /register.php — form. POST — validate, create unverified account, email OTP. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';
require INCLUDES_PATH . '/mailer.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: questions.php');
    exit;
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (isBlank($username)) {
        $errors['username'] = 'Please enter a username.';
    } elseif (mb_strlen($username) > 50) {
        $errors['username'] = 'Username must be 50 characters or fewer.';
    } elseif (findUserByUsername($pdo, $username)) {
        $errors['username'] = 'That username is already taken.';
    }

    if (isBlank($email)) {
        $errors['email'] = 'Please enter your email.';
    } elseif (!validEmail($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (findUserByEmail($pdo, $email)) {
        $errors['email'] = 'An account with that email already exists.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }




    //create OTP and send email//
    if (!$errors) {
        try {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = date('Y-m-d H:i:s', time() + OTP_TTL_MINUTES * 60);

            $newId = insertPendingUser($pdo, $username, $email, password_hash($password, PASSWORD_DEFAULT), $otp, $expiresAt);

            if (!sendOtpEmail($email, $username, $otp)) {
                // No usable account without a code: remove it so the person can try again.
                deleteUser($pdo, $newId);
                $errors['general'] = 'Sorry — the verification email could not be sent, so the account was not created. Please try again later.';
            } else {
                header('Location: verify.php?email=' . urlencode($email));
                exit;
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Sorry — your account could not be created. Please try again.';
        }
    }
}

$title = 'Register';
ob_start();
require TEMPLATES_PATH . '/register.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
