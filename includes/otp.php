<?php
/**
 * OTP (One-Time Password) helper functions.
 * Requires $connect (mysqli connection) to already be set up (include db.php first).
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/mailer.php';

const OTP_VALID_MINUTES   = 10;
const OTP_MAX_ATTEMPTS    = 5;
const OTP_RESEND_COOLDOWN = 60; // seconds

/**
 * Generates a new 6-digit OTP, stores it in the DB, and emails it.
 * Any older, unused OTPs for the same email+purpose are invalidated first.
 *
 * @return array ['success' => bool, 'error' => string|null]
 */
function generate_and_send_otp($connect, $email, $name, $purpose) {
    $email_esc = mysqli_real_escape_string($connect, $email);
    $purpose_esc = mysqli_real_escape_string($connect, $purpose);

    // Invalidate any previous unused OTPs for this email/purpose
    mysqli_query($connect, "UPDATE otps SET used=1 WHERE email='$email_esc' AND purpose='$purpose_esc' AND used=0");

    $otp_code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date('Y-m-d H:i:s', time() + (OTP_VALID_MINUTES * 60));
    $otp_esc = mysqli_real_escape_string($connect, $otp_code);

    $sql = "INSERT INTO otps (email, otp_code, purpose, expires_at) VALUES ('$email_esc', '$otp_esc', '$purpose_esc', '$expires_at')";
    if (!mysqli_query($connect, $sql)) {
        return ['success' => false, 'error' => 'Could not generate OTP: ' . mysqli_error($connect)];
    }

    $result = send_otp_email($email, $name, $otp_code, $purpose);
    if (!$result['success']) {
        return ['success' => false, 'error' => 'Could not send OTP email: ' . $result['error']];
    }

    return ['success' => true, 'error' => null];
}

/**
 * Verifies a submitted OTP code for the given email/purpose.
 *
 * @return array ['success' => bool, 'error' => string|null]
 */
function verify_otp($connect, $email, $purpose, $submittedCode) {
    $email_esc = mysqli_real_escape_string($connect, $email);
    $purpose_esc = mysqli_real_escape_string($connect, $purpose);

    $sql = "SELECT * FROM otps WHERE email='$email_esc' AND purpose='$purpose_esc' AND used=0 ORDER BY id DESC LIMIT 1";
    $res = mysqli_query($connect, $sql);

    if (!$res || mysqli_num_rows($res) === 0) {
        return ['success' => false, 'error' => 'No active code found. Please request a new one.'];
    }

    $row = mysqli_fetch_assoc($res);

    if ($row['attempts'] >= OTP_MAX_ATTEMPTS) {
        return ['success' => false, 'error' => 'Too many incorrect attempts. Please request a new code.'];
    }

    if (strtotime($row['expires_at']) < time()) {
        return ['success' => false, 'error' => 'This code has expired. Please request a new one.'];
    }

    if (!hash_equals($row['otp_code'], trim($submittedCode))) {
        mysqli_query($connect, "UPDATE otps SET attempts = attempts + 1 WHERE id = " . (int) $row['id']);
        return ['success' => false, 'error' => 'Incorrect code. Please try again.'];
    }

    mysqli_query($connect, "UPDATE otps SET used = 1 WHERE id = " . (int) $row['id']);
    return ['success' => true, 'error' => null];
}
