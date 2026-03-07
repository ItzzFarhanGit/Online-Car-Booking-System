<?php
session_start();
include 'db.php'; // Include your database connection if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us - Online Car Booking System</title>
<link rel="Stylesheet" href="about.css">
</head>
<body>

<header>
  <div class="logo">Online Car Booking</div>
  <nav>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="caravailable.php">CARS</a></li>
      <li><a href="booking.php">BOOK NOW</a></li>
      <li><a href="about.php" class="active">ABOUT</a></li>
      <li><a href="contact.php">CONTACT</a></li>
      <?php if(isset($_SESSION['username'])): ?>
        <li><a href="logout.php" class="btn-logout">LOGOUT</a></li>
      <?php else: ?>
        <li><a href="login.php" class="btn-login">LOGIN</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<section class="about-box">
  <h1>About Online Car Booking System</h1>
  <p>
    The Online Car Booking System is a simple and user-friendly platform that allows customers to easily book cars online.
    Built using HTML, CSS, and PHP, this system provides a fast and convenient way for users to check car availability,
    book their preferred car, and manage bookings efficiently.
  </p>

  <h2>Project Objectives</h2>
  <ul>
    <li>Provide a User-Friendly Booking Interface.</li>
    <li>Display Available Cars in Real-Time.</li>
    <li>Introduce Secure Authentication for Users and Admin.</li>
    <li>Enable Admin to Manage Cars and Bookings.</li>
    <li>Generate Confirmation and Receipts for Bookings.</li>
    <li>Collect Customer Feedback and Reviews.</li>
    <li>Keep Basic Reporting and Analytics.</li>
  </ul>

  <h2>Motivation</h2>
  <p>
    Many Online Car Booking Systems are either complex, expensive, or require advanced technologies.
    This project aims to create a simple, affordable, and effective system suitable for small businesses or classroom projects.
    It also helps students learn practical web development skills.
  </p>
</section>

<footer>
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
