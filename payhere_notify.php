<?php
/**
 * PayHere calls this URL directly (server-to-server) after a payment attempt.
 * It is NEVER opened in a customer's browser, so don't rely on this page for
 * showing anything to the user - it only updates the database.
 *
 * IMPORTANT: PayHere can only reach this file once the project is hosted on
 * a real, publicly-accessible domain (it will not work on localhost/XAMPP).
 */
include 'db.php';
include 'config.php';
include 'includes/payhere.php';

// Log every incoming notification for debugging (safe to delete this file later).
@file_put_contents(__DIR__ . '/payhere_notify.log', date('Y-m-d H:i:s') . ' ' . json_encode($_POST) . "\n", FILE_APPEND);

$merchant_id = $_POST['merchant_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$payhere_amount = $_POST['payhere_amount'] ?? '';
$payhere_currency = $_POST['payhere_currency'] ?? '';
$status_code = $_POST['status_code'] ?? '';
$md5sig = $_POST['md5sig'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';

if ($order_id === '' || $md5sig === '') {
    http_response_code(400);
    exit('Missing parameters');
}

if (!payhere_verify_signature($merchant_id, $order_id, $payhere_amount, $payhere_currency, $status_code, $md5sig)) {
    http_response_code(400);
    exit('Invalid signature');
}

$order_id_esc = mysqli_real_escape_string($connect, $order_id);
$payment_id_esc = mysqli_real_escape_string($connect, $payment_id);

$new_status = 'Unpaid';
if ((string) $status_code === '2') {
    $new_status = 'Paid';
} elseif ((string) $status_code === '0') {
    $new_status = 'Pending';
} elseif ((string) $status_code === '-1') {
    $new_status = 'Cancelled';
} else {
    $new_status = 'Failed';
}

mysqli_query($connect, "UPDATE bookings SET payment_status='$new_status', payment_id='$payment_id_esc' WHERE order_id='$order_id_esc'");

if ($new_status === 'Paid') {
    mysqli_query($connect, "UPDATE bookings SET status='Confirmed' WHERE order_id='$order_id_esc'");
}

http_response_code(200);
echo 'OK';
