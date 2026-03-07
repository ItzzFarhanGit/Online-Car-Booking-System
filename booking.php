<?php
session_start();
include 'db.php';
$msg = "";

if(isset($_POST['submit'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    $return_date = $_POST['return_date'];
    $return_time = $_POST['return_time'];
    $car_type = $_POST['car_type'];

    if(empty($fullname) || empty($email) || empty($pickup_date) || empty($pickup_time) || empty($return_date) || empty($return_time) || empty($car_type)) {
        $msg = "Please fill all fields properly!";
    } else {
        $pickup_datetime = $pickup_date . " " . $pickup_time;
        $return_datetime = $return_date . " " . $return_time;

        $sql = "INSERT INTO bookings (fullname, email, pickup_datetime, return_datetime, car_type)
                VALUES ('$fullname', '$email', '$pickup_datetime', '$return_datetime', '$car_type')";

        if(mysqli_query($connect, $sql)){
            header("Location: bookingsuccess.php"); // Redirect success page
            exit;
        } else {
            $msg = "Error: " . mysqli_error($connect);
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Your Car | Online Car Booking System</title>
  <link rel="Stylesheet" href="booking.css">
</head>
<body>

<header>
  <div class="logo">Online Car Booking</div>
  <nav>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="caravailable.php">CARS</a></li>
      <li><a href="booking.php" class="active">BOOK NOW</a></li>
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

<section class="booking">
 <form class="booking-form" method="POST">
    <h2>Book Your Ride</h2>

    <?php if($msg != "") { echo "<p style='text-align:center;color:yellow'>$msg</p>"; } ?>

    <label>Full Name</label>
    <input type="text" name="fullname" required>

    <label>Email Address</label>
    <input type="email" name="email" required>

    <label>Pickup Date</label>
    <input type="date" name="pickup_date" required>

    <label>Pickup Time</label>
    <input type="time" name="pickup_time" required>

    <label>Return Date</label>
    <input type="date" name="return_date" required>

    <label>Return Time</label>
    <input type="time" name="return_time" required>

    <label>Select Car Type</label>
    <select name="car_type" required>
      <option value="">Select Car Type</option>
      <option value="sedan">Sedan</option>
      <option value="suv">SUV</option>
      <option value="luxury">Luxury</option>
    </select>

    <button type="submit" name="submit">Book Now</button>

    <?php if($msg == "Booking Successful!") { ?>
<script>
    alert("Your Car Booking is Completed Successfully!");
</script>
<?php } ?>


</form>

</section>

<footer>
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
