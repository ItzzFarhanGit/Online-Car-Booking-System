<?php
$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Cancelled | Online Car Booking</title>
  <link rel="Stylesheet" href="bookingsuccess.css">
</head>
<body>

  <div class="success-box">
      <div class="tick" style="color:#d9534f;">✕</div>
      <h2>Payment Cancelled</h2>
      <p>You cancelled the payment before it was completed. Your booking has been saved, but it isn't confirmed yet. You can try paying again anytime.</p>
      <?php if ($booking_id > 0): ?>
        <a href="payment.php?booking_id=<?php echo $booking_id; ?>" class="btn">Try Again</a>
      <?php endif; ?>
      <a href="home.php" class="btn">Go to Home</a>
  </div>

</body>
</html>
