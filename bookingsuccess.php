<?php
include 'db.php';

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
$booking = null;

if ($booking_id > 0) {
    $result = mysqli_query($connect, "SELECT * FROM bookings WHERE id=" . $booking_id);
    if ($result && mysqli_num_rows($result) > 0) {
        $booking = mysqli_fetch_assoc($result);
    }
}

// PayHere confirms payment via a server-to-server call (payhere_notify.php),
// not on this page directly, so we re-check the database for the latest status.
$status = $booking ? $booking['payment_status'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Status | Online Car Booking</title>
  <link rel="Stylesheet" href="bookingsuccess.css">
  <?php if ($status === 'Pending' || $status === null): ?>
  <meta http-equiv="refresh" content="5">
  <?php endif; ?>
</head>
<body>

  <div class="success-box">
    <?php if ($status === 'Paid'): ?>
      <div class="tick">✔</div>
      <h2>Booking Successful!</h2>
      <p>Your payment was received and your booking is confirmed. Thank you for choosing our service. We wish you a safe and happy journey!</p>
    <?php elseif ($status === 'Failed' || $status === 'Cancelled'): ?>
      <div class="tick" style="color:#d9534f;">✕</div>
      <h2>Payment Not Completed</h2>
      <p>Your booking was saved but the payment didn't go through. Please try paying again.</p>
      <?php if ($booking_id > 0): ?><a href="payment.php?booking_id=<?php echo $booking_id; ?>" class="btn">Try Again</a><?php endif; ?>
    <?php else: ?>
      <div class="tick" style="color:#f0ad4e;">…</div>
      <h2>Confirming Your Payment</h2>
      <p>We're waiting for PayHere to confirm your payment. This page will refresh automatically. This can take up to a minute.</p>
    <?php endif; ?>

      <a href="home.php" class="btn">Go to Home</a>
      <a href="mybookings.php" class="btn">View My Bookings</a>
  </div>

</body>
</html>
