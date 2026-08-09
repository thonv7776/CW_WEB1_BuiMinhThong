<?php
/** GET /verify.php?email= — OTP entry form. POST — check code or resend it. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';
require INCLUDES_PATH . '/db-functions.php';
require INCLUDES_PATH . '/validate.php';
require INCLUDES_PATH . '/mailer.php';

$email = trim((string) ($_GET['email'] ?? ($_POST['email'] ?? '')));
$error = null;
$notice = null;

// One-time dev flash set by register.php/resend when SMTP isn't configured.
$devOtp = $_SESSION['dev_otp'] ?? null;
unset($_SESSION['dev_otp']);

if ($email === '' || !validEmail($email)) {
    header('Location: register.php');
    exit;
}

$user = findUserByEmail($pdo, $email);

if (!$user) {
    header('Location: register.php');
    exit;
}

if ((int) $user['is_verified'] === 1) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'verify';

    if ($action === 'resend') {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', time() + OTP_TTL_MINUTES * 60);
        setUserOtp($pdo, $user['user_id'], $otp, $expiresAt);

        if (sendOtpEmail($email, $user['username'], $otp)) {
            $notice = 'A new code has been emailed to you.';
        } else {
            $devOtp = $otp;
            $notice = 'Could not send the email — here is your new code for testing.';
        }
    } else {
        $code = trim($_POST['otp'] ?? '');
        $expired = empty($user['otp_expires_at']) || strtotime($user['otp_expires_at']) < time();

        if ($user['otp_code'] === null || $code === '') {
            $error = 'Please enter the code we emailed you.';
        } elseif ($expired) {
            $error = 'That code has expired. Request a new one below.';
        } elseif (!hash_equals((string) $user['otp_code'], $code)) {
            $error = 'That code is incorrect. Please try again.';
        } else {
            markUserVerified($pdo, $user['user_id']);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header('Location: questions.php?verified=1');
            exit;
        }
    }
}

$title = 'Verify your email';
ob_start();
require TEMPLATES_PATH . '/verify.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
