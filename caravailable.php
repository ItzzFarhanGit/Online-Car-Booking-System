<?php
session_start();
include 'db.php'; // DB connection

// Fetch cars from database
$cars_query = "SELECT * FROM cars"; 
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

      <div class="car-box">
        <img src="IMAGES/Hyundai i20 Car.jpg" alt="Car 1">
        <h3>Hyundai i20</h3>
        <p><strong>Price: Rs.10,000 / Day</strong></p>
        <a href="booking.php" class="btn">Book Now</a>
      </div>

      <div class="car-box">
        <img src="IMAGES/Maruti Swift Car.jpg" alt="Car 2">
        <h3>Maruti Swift</h3>
        <p><strong>Price: Rs.15,000 / Day</strong></p>
        <a href="booking.php" class="btn">Book Now</a>
      </div>

      <div class="car-box">
        <img src="IMAGES/Toyota Innova Car.jpg" alt="Car 3">
        <h3>Toyota Innova</h3>
        <p><strong>Price: Rs.20,000 / Day</strong></p>
        <a href="booking.php" class="btn">Book Now</a>
      </div>

      <div class="car-box">
        <img src="IMAGES/Honda City Car.jpg" alt="Car 4">
        <h3>Honda City</h3>
        <p><strong>Price: Rs.25,000 / Day</strong></p>
        <a href="booking.php" class="btn">Book Now</a>
      </div>
      
      <br>
      <br>
      <br>
         <?php
      if(mysqli_num_rows($cars_result) > 0){
          while($car = mysqli_fetch_assoc($cars_result)){
              echo '<div class="car-box">';
              echo '<img src="IMAGES/'.$car['image'].'" alt="'.$car['name'].'">';
              echo '<h3>'.$car['name'].'</h3>';
              echo '<p><strong>Price: Rs.'.$car['price'].' / Day</strong></p>';
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
