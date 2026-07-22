<?php
/**
 * Email sending helper (built on PHPMailer + Gmail SMTP).
 * See config.php to set your Gmail address / App Password.
 */

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an OTP code by email.
 *
 * @param string $toEmail   Recipient email address
 * @param string $toName    Recipient display name
 * @param string $otpCode   The OTP code to send
 * @param string $purpose   'signup' or 'reset' (changes the wording of the email)
 * @return array ['success' => bool, 'error' => string|null]
 */
function send_otp_email($toEmail, $toName, $otpCode, $purpose = 'signup') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = str_replace(' ', '', SMTP_PASSWORD); // Gmail App Passwords are shown with spaces; strip them just in case
        $mail->SMTPSecure = (SMTP_SECURE === 'smtps') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 15;

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);

        if ($purpose === 'reset') {
            $mail->Subject = 'Password Reset Code - Online Car Booking';
            $heading = 'Reset Your Password';
            $body    = 'We received a request to reset your password. Use the code below to continue:';
        } else {
            $mail->Subject = 'Verify Your Email - Online Car Booking';
            $heading = 'Verify Your Email';
            $body    = 'Thanks for signing up! Use the code below to verify your email address and finish creating your account:';
        }

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width:480px; margin:auto; padding:24px; border:1px solid #eee; border-radius:8px;'>
                <h2 style='color:#222;'>{$heading}</h2>
                <p style='color:#555;'>{$body}</p>
                <p style='font-size:32px; letter-spacing:8px; font-weight:bold; color:#0b5ed7; text-align:center; margin:24px 0;'>{$otpCode}</p>
                <p style='color:#777; font-size:13px;'>This code will expire in 10 minutes. If you did not request this, you can safely ignore this email.</p>
                <p style='color:#aaa; font-size:12px; margin-top:24px;'>Online Car Booking System</p>
            </div>
        ";
        $mail->AltBody = "Your verification code is: $otpCode (expires in 10 minutes).";

        $mail->send();
        return ['success' => true, 'error' => null];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}
