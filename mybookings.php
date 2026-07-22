<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$bookings_result = mysqli_query($connect, "SELECT * FROM bookings WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings | Online Car Booking</title>
  <link rel="Stylesheet" href="mybookings.css">
</head>
<body>

<header>
  <div class="logo">Online Car Booking</div>
  <nav>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="caravailable.php">CARS</a></li>
      <li><a href="booking.php">BOOK NOW</a></li>
      <li><a href="about.php">ABOUT</a></li>
      <li><a href="contact.php">CONTACT</a></li>
      <li><a href="mybookings.php" class="active">MY BOOKINGS</a></li>
      <li><a href="logout.php" class="btn-login">LOGOUT</a></li>
    </ul>
  </nav>
</header>

<section class="mybookings-section">
  <h1>My Bookings</h1>

  <?php if (mysqli_num_rows($bookings_result) > 0): ?>
    <div class="bookings-list">
      <?php while ($b = mysqli_fetch_assoc($bookings_result)): ?>
        <div class="booking-card">
          <div class="booking-card-header">
            <h3><?php echo htmlspecialchars($b['car_type']); ?></h3>
            <span class="status status-<?php echo strtolower($b['status']); ?>"><?php echo htmlspecialchars($b['status']); ?></span>
          </div>
          <p><strong>Booking ID:</strong> #<?php echo $b['id']; ?></p>
          <p><strong>Pickup:</strong> <?php echo htmlspecialchars($b['pickup_datetime']); ?></p>
          <p><strong>Return:</strong> <?php echo htmlspecialchars($b['return_datetime']); ?></p>
          <p><strong>Amount:</strong> Rs. <?php echo number_format((float) $b['total_amount'], 2); ?></p>
          <p><strong>Payment:</strong> <?php echo htmlspecialchars($b['payment_status']); ?>
            <?php if ($b['payment_status'] !== 'Paid'): ?>
              &nbsp;<a href="payment.php?booking_id=<?php echo $b['id']; ?>">(Pay Now)</a>
            <?php endif; ?>
          </p>
          <p><strong>Booked On:</strong> <?php echo htmlspecialchars($b['created_at']); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="no-bookings">You haven't made any bookings yet. <a href="booking.php">Book a car now</a>.</p>
  <?php endif; ?>
</section>

<footer>
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
