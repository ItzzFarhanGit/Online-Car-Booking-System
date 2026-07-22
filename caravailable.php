<?php
session_start();
include 'db.php';

$cars_query = "SELECT * FROM cars WHERE status='Available' ORDER BY id ASC";
$cars_result = mysqli_query($connect, $cars_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Available Cars - Online Car Booking System</title>
  <link rel="Stylesheet" href="caravailable.css">
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo">Online Car Booking</div>
    <nav>
      <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="caravailable.php" class="active">CARS</a></li>
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

  <section class="cars-section">
    <h1>Available Cars</h1>
    <div class="cars-container">

      <?php
      if(mysqli_num_rows($cars_result) > 0){
          while($car = mysqli_fetch_assoc($cars_result)){
              echo '<div class="car-box">';
              echo '<img src="IMAGES/'.htmlspecialchars($car['image']).'" alt="'.htmlspecialchars($car['name']).'">';
              echo '<h3>'.htmlspecialchars($car['name']).'</h3>';
              echo '<p><strong>Price: Rs.'.number_format($car['price'], 2).' / Day</strong></p>';
              echo '<a href="booking.php?car_id='.$car['id'].'" class="btn">Book Now</a>';
              echo '</div>';
          }
      } else {
          echo '<p style="color: yellow;">No cars available at the moment.</p>';
      }
      ?>

    </div>
  </section>

  <footer>
    <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
  </footer>

</body>
</html>
