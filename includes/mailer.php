<?php
/**
 * mailer.php — sends email through PHPMailer/SMTP.
 *
 *  - sendOtpEmail()     registration verification code to the student
 *  - sendContactEmail() contact-form message to the system administrator
 *
 * Requires vendor/phpmailer (see composer.json) and real SMTP_* values in
 * .env (see .env.example). Both functions return false instead of throwing
 * when email cannot be sent.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/** Build an SMTP-ready PHPMailer, or null if the library/.env is not set up. */
function createMailer()
{
    $autoload = ROOT_PATH . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        return null; // composer install hasn't been run yet
    }
    require_once $autoload;

    if (SMTP_HOST === '' || SMTP_USERNAME === '' || SMTP_PASSWORD === '') {
        return null; // .env not filled in yet
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    return $mail;
}

function sendOtpEmail($toEmail, $toName, $otp)
{
    try {
        $mail = createMailer();
        if ($mail === null) {
            return false;
        }

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

/** Email a contact-form message to the administrator (the SMTP account's inbox). */
function sendContactEmail($fromName, $fromEmail, $subject, $body)
{
    try {
        $mail = createMailer();
        if ($mail === null) {
            return false;
        }

        $mail->addAddress(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addReplyTo($fromEmail, $fromName);
        $mail->Subject = 'Contact form: ' . $subject;
        $mail->Body = "From: {$fromName} <{$fromEmail}>\n\n{$body}";

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        return false;
    }
}
