<?php
// Start session for login/logout functionality
session_start();

// Optional: Include database connection if needed in future
// include 'db.php';
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

  <footer>
    <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
  </footer>

</body>
</html>
