<?php
/**
 * STANDALONE SMTP DIAGNOSTIC TOOL
 * ---------------------------------
 * Open this file directly in your browser, e.g.:
 *   http://yourdomain.com/test_email.php
 *   (or http://localhost/your-folder/test_email.php)
 *
 * It sends ONE test email to your own SMTP_USERNAME address and prints the
 * full, word-for-word conversation between this server and Gmail. Copy the
 * red/verbose text it prints and share it — the exact wording (e.g.
 * "Username and Password not accepted" vs "Application-specific password
 * required") tells us exactly what's wrong.
 *
 * DELETE this file once emails are working (it exposes SMTP debug info).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/PHPMailer/Exception.php';
require_once __DIR__ . '/includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: text/html; charset=utf-8');
echo "<pre style='background:#111;color:#0f0;padding:20px;white-space:pre-wrap;font-size:13px;'>";

echo "Config being used:\n";
echo "  SMTP_HOST     = " . SMTP_HOST . "\n";
echo "  SMTP_PORT     = " . SMTP_PORT . "\n";
echo "  SMTP_SECURE   = " . SMTP_SECURE . "\n";
echo "  SMTP_USERNAME = " . SMTP_USERNAME . "\n";
echo "  SMTP_PASSWORD = " . str_repeat('*', max(0, strlen(SMTP_PASSWORD) - 4)) . substr(SMTP_PASSWORD, -4) . " (length: " . strlen(str_replace(' ', '', SMTP_PASSWORD)) . " chars after removing spaces - should be 16)\n\n";

if (SMTP_USERNAME === 'youremail@gmail.com' || SMTP_PASSWORD === 'your16digitapppassword') {
    echo "\n\033[0m>>> STOP: config.php still has the placeholder values. Edit config.php first. <<<\n";
    echo "</pre>";
    exit;
}

echo "--- Connecting to Gmail (full debug output below) ---\n\n";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // full conversation
    $mail->Debugoutput = function ($str, $level) {
        echo htmlspecialchars($str) . "\n";
    };

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = str_replace(' ', '', SMTP_PASSWORD);
    $mail->SMTPSecure = (SMTP_SECURE === 'smtps') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->Timeout    = 15;

    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress(SMTP_USERNAME); // send the test email to yourself
    $mail->isHTML(false);
    $mail->Subject = 'Test email from Online Car Booking';
    $mail->Body    = 'If you received this, your SMTP settings are correct!';

    $mail->send();

    echo "\n\n>>> SUCCESS! Check the inbox of " . SMTP_USERNAME . " for the test email. <<<\n";
} catch (Exception $e) {
    echo "\n\n>>> FAILED: " . $mail->ErrorInfo . " <<<\n";
}

echo "</pre>";
