<?php
session_start();
include 'db.php';

// Pull up to 4 available cars to feature on the home page
$featured_cars_result = mysqli_query($connect, "SELECT * FROM cars WHERE status='Available' ORDER BY id ASC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Car Booking</title>
  <link rel="Stylesheet" href="home.css">
</head>
<body>

  <header>
    <div class="logo">Online Car Booking</div>
    <nav>
      <ul>
        <li><a href="home.php" class="active">HOME</a></li>
        <li><a href="caravailable.php">CARS</a></li>
        <li><a href="booking.php">BOOK NOW</a></li>
        <li><a href="about.php">ABOUT</a></li>
        <li><a href="contact.php">CONTACT</a></li>
        <li>
          <?php
          if(isset($_SESSION['username'])){
              echo '<a href="mybookings.php">MY BOOKINGS</a>';
          }
          ?>
        </li>
        <li>
          <?php
          // Dynamic login/logout button
          if(isset($_SESSION['username'])){
              echo '<a href="logout.php" class="btn-login">LOGOUT</a>';
          } else {
              echo '<a href="login.php" class="btn-login">LOGIN</a>';
          }
          ?>
        </li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to Online Car Booking System</h1>
      <p>Book your favorite car anytime, anywhere — simple, fast and secure.</p>
      <a href="booking.php" class="btn">Book a Car</a>
    </div>
  </section>

  <section class="section-block featured-cars">
    <h2>Featured Cars</h2>
    <p class="section-sub">A few of the rides available for booking right now</p>

    <div class="cars-grid">
      <?php if ($featured_cars_result && mysqli_num_rows($featured_cars_result) > 0): ?>
        <?php while ($car = mysqli_fetch_assoc($featured_cars_result)): ?>
          <div class="car-card">
            <img src="IMAGES/<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h3><?php echo htmlspecialchars($car['name']); ?></h3>
              <p>Rs. <?php echo number_format($car['price'], 2); ?> / Day</p>
              <a href="booking.php?car_id=<?php echo $car['id']; ?>" class="btn">Book Now</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="color:#666;">No cars available at the moment. Please check back soon.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="section-block why-us">
    <h2>Why Choose Us</h2>
    <p class="section-sub">Everything you need for a smooth, worry-free rental</p>

    <div class="features-grid">
      <div class="feature-card">
        <div class="icon">🚗</div>
        <h3>Wide Car Selection</h3>
        <p>From hatchbacks to SUVs, pick the car that fits your trip and your budget.</p>
      </div>
      <div class="feature-card">
        <div class="icon">🔒</div>
        <h3>Secure Online Payments</h3>
        <p>Pay safely through PayHere with encrypted, verified transactions — no cash hassle.</p>
      </div>
      <div class="feature-card">
        <div class="icon">📧</div>
        <h3>Verified Accounts</h3>
        <p>Every signup and password reset is protected with a one-time email verification code.</p>
      </div>
      <div class="feature-card">
        <div class="icon">🕒</div>
        <h3>Book Anytime</h3>
        <p>Reserve a car in minutes, any time of day, and track every booking from your account.</p>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <h2>Ready to hit the road?</h2>
    <p>Browse our available cars and book your next ride in just a few clicks.</p>
    <a href="caravailable.php" class="btn">View All Cars</a>
  </section>

  <footer>
    <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
  </footer>

</body>
</html>
