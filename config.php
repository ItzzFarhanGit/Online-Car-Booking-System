<?php
/**
 * ============================================================
 *  CENTRAL CONFIGURATION FILE
 * ============================================================
 * Fill in the values below with YOUR OWN credentials before
 * you host this project. Nothing will work (emails / payments)
 * until you do this.
 *
 * 1) EMAIL (OTP) SETTINGS  -> uses Gmail SMTP, completely free.
 * 2) PAYHERE (PAYMENT) SETTINGS -> uses PayHere Sandbox, free to test.
 * ============================================================
 */

// ---------------------------------------------------------------
// 1) EMAIL / SMTP SETTINGS (used to send OTP codes)
// ---------------------------------------------------------------
// How to get a Gmail "App Password" (this is NOT your normal Gmail password):
//   1. Go to https://myaccount.google.com/security
//   2. Turn ON "2-Step Verification" for your Google account (required).
//   3. Go to https://myaccount.google.com/apppasswords
//   4. Create an App Password (choose "Mail" as the app).
//   5. Google gives you a 16-letter code like "abcd efgh ijkl mnop".
//      Paste it below (spaces don't matter).
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
// If your host blocks port 587, try: SMTP_PORT 465 and SMTP_SECURE 'smtps' instead.
define('SMTP_SECURE', 'tls'); // 'tls' (port 587) or 'smtps' (port 465)
define('SMTP_USERNAME', 'farhanfaleel9@gmail.com');   // <-- TODO: put YOUR Gmail address here
define('SMTP_PASSWORD', 'qblj haou nmoj tmxb'); // <-- TODO: put YOUR Gmail App Password here (remove spaces!)
define('SMTP_FROM_EMAIL', SMTP_USERNAME);
define('SMTP_FROM_NAME', 'Online Car Booking');

// ---------------------------------------------------------------
// 2) PAYHERE (PAYMENT GATEWAY) SETTINGS - Sandbox / Test Mode
// ---------------------------------------------------------------
// How to get these (free, takes 2 minutes, no business documents needed for sandbox):
//   1. Go to https://sandbox.payhere.lk/ and click "Create Sandbox Account".
//   2. After logging in, go to Side Menu > Integrations to find your
//      SANDBOX "Merchant ID".
//   3. On the same Integrations page, click "Add Domain/App", type the
//      domain you will host this project on (e.g. mycarbooking.com),
//      and PayHere will generate a "Merchant Secret" for that domain.
//      (On sandbox this is usually approved instantly; on a live/real
//      account it can take up to 24 hours.)
//   4. Paste both values below.
//
// IMPORTANT: PayHere's payment notification (notify_url) can ONLY reach
// a server that is publicly hosted with a real domain (and HTTPS) - it
// will NOT work on localhost/XAMPP. Test the payment step after you
// upload this project to your real hosting.
define('PAYHERE_MERCHANT_ID', '1237075');     // <-- TODO: replace with YOUR sandbox Merchant ID
define('PAYHERE_MERCHANT_SECRET', 'MjcxMjA4ODM1MzI5NjIzODkxNDM4Mzk4NjkxNDkyMzk3NTMzNTU3

'); // <-- TODO: replace with YOUR Merchant Secret
define('PAYHERE_SANDBOX', true); // set to false ONLY when you switch to a real/live PayHere account

// ---------------------------------------------------------------
// SITE BASE URL (auto-detected - usually you don't need to change this)
// ---------------------------------------------------------------
function site_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Normalize when called from inside /admin or other subfolders isn't needed
    // because we always build absolute paths from the site root below.
    return rtrim($protocol . $host, '/');
}

// Root URL path of the project (e.g. "/car_booking" on localhost, or "" on a real domain).
function site_root_path() {
    // BASE_PATH can be overridden manually below if auto-detection ever gets it wrong.
    if (defined('BASE_PATH_OVERRIDE')) {
        return BASE_PATH_OVERRIDE;
    }
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    // Remove known sub-paths (admin/, otp handlers, etc.) to find the project root
    $script = preg_replace('#/admin/.*$#', '/', $script);
    $script = preg_replace('#/[^/]+$#', '/', $script);
    return rtrim($script, '/');
}
?>
