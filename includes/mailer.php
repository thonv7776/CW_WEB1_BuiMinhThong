<?php
/**
 * mailer.php — sends the registration OTP email via PHPMailer/SMTP.
 *
 * Requires `composer install` to have fetched vendor/phpmailer/phpmailer
 * (see composer.json) and real SMTP_* values in .env (see .env.example).
 * Both are the student's own setup step — nothing here works out of the box.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Send a one-time code to $toEmail. Returns true on success, false on any
 * failure (missing vendor/, missing/incomplete .env, SMTP rejection, ...) —
 * callers should treat false as "could not email the code, try again later"
 * rather than a fatal error.
 */
function sendOtpEmail($toEmail, $toName, $otp)
{
    $autoload = ROOT_PATH . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        return false; // composer install hasn't been run yet
    }
    require_once $autoload;

    if (SMTP_HOST === '' || SMTP_USERNAME === '' || SMTP_PASSWORD === '') {
        return false; // .env not filled in yet
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        $mail->Subject = 'Your Student Q&A verification code';
        $mail->Body = "Hi {$toName},\n\n"
            . "Your verification code is: {$otp}\n\n"
            . 'This code expires in ' . OTP_TTL_MINUTES . " minutes.\n\n"
            . "If you didn't request this, you can ignore this email.";

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        return false;
    }
}
