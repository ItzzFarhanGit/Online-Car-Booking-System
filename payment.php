<?php
session_start();
include 'db.php';
include 'config.php';
include 'includes/payhere.php';

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    header("Location: booking.php");
    exit;
}

$result = mysqli_query($connect, "SELECT * FROM bookings WHERE id=" . $booking_id);
if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: booking.php");
    exit;
}
$booking = mysqli_fetch_assoc($result);

// Already paid? Just send them straight to the success page.
if ($booking['payment_status'] === 'Paid') {
    header("Location: bookingsuccess.php?booking_id=" . $booking_id);
    exit;
}

$nameParts = preg_split('/\s+/', trim($booking['fullname']), 2);
$first_name = $nameParts[0] ?? 'Customer';
$last_name = $nameParts[1] ?? '.';

$amount = (float) $booking['total_amount'];
$currency = 'LKR';
$order_id = $booking['order_id'];
$hash = payhere_generate_hash($order_id, $amount, $currency);

$base = rtrim(site_base_url() . site_root_path(), '/');
$return_url = $base . '/bookingsuccess.php?booking_id=' . $booking_id;
$cancel_url = $base . '/paymentcancelled.php?booking_id=' . $booking_id;
$notify_url = $base . '/payhere_notify.php';

$configPlaceholder = (PAYHERE_MERCHANT_ID === 'YOUR_SANDBOX_MERCHANT_ID');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment | Online Car Booking</title>
  <link rel="Stylesheet" href="booking.css">
  <style>
    .pay-box { max-width: 480px; margin: 60px auto; background:#1f1f1f; color:#fff; padding:30px; border-radius:10px; }
    .pay-box h2 { text-align:center; margin-bottom:20px; }
    .pay-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #333; font-size:15px; }
    .pay-total { font-size:20px; font-weight:bold; color:#ffd700; padding-top:14px; }
    .pay-btn { width:100%; margin-top:24px; padding:14px; border:none; border-radius:6px; background:#0b5ed7; color:#fff; font-size:16px; cursor:pointer; }
    .pay-btn:hover { background:#094bb0; }
    .warn { background:#442222; color:#ffb3b3; padding:10px; border-radius:6px; font-size:13px; margin-bottom:16px; }
  </style>
</head>
<body>

<div class="pay-box">
  <h2>Confirm & Pay</h2>

  <?php if ($configPlaceholder): ?>
    <div class="warn">
      Heads up: PayHere isn't configured yet. Open <code>config.php</code> and add your
      sandbox Merchant ID &amp; Merchant Secret, otherwise this payment will fail.
    </div>
  <?php endif; ?>

  <div class="pay-row"><span>Car</span><span><?php echo htmlspecialchars($booking['car_type']); ?></span></div>
  <div class="pay-row"><span>Pickup</span><span><?php echo htmlspecialchars($booking['pickup_datetime']); ?></span></div>
  <div class="pay-row"><span>Return</span><span><?php echo htmlspecialchars($booking['return_datetime']); ?></span></div>
  <div class="pay-row"><span>Order ID</span><span><?php echo htmlspecialchars($order_id); ?></span></div>
  <div class="pay-row pay-total"><span>Total</span><span>Rs. <?php echo number_format($amount, 2); ?></span></div>

  <form method="POST" action="<?php echo payhere_checkout_url(); ?>">
    <input type="hidden" name="merchant_id" value="<?php echo htmlspecialchars(PAYHERE_MERCHANT_ID); ?>">
    <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($return_url); ?>">
    <input type="hidden" name="cancel_url" value="<?php echo htmlspecialchars($cancel_url); ?>">
    <input type="hidden" name="notify_url" value="<?php echo htmlspecialchars($notify_url); ?>">
    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
    <input type="hidden" name="items" value="<?php echo htmlspecialchars($booking['car_type']); ?> Rental">
    <input type="hidden" name="currency" value="<?php echo $currency; ?>">
    <input type="hidden" name="amount" value="<?php echo number_format($amount, 2, '.', ''); ?>">
    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($booking['email']); ?>">
    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($booking['phone']); ?>">
    <input type="hidden" name="address" value="<?php echo htmlspecialchars($booking['address']); ?>">
    <input type="hidden" name="city" value="<?php echo htmlspecialchars($booking['city']); ?>">
    <input type="hidden" name="country" value="Sri Lanka">
    <input type="hidden" name="hash" value="<?php echo $hash; ?>">
    <button type="submit" class="pay-btn">Pay with PayHere (Sandbox)</button>
  </form>
</div>

</body>
</html>
