<?php
session_start();
include 'db.php';
include 'includes/payhere.php';
$msg = "";

$cars_result = mysqli_query($connect, "SELECT * FROM cars WHERE status='Available' ORDER BY id ASC");
$preselect_car_id = isset($_GET['car_id']) ? (int) $_GET['car_id'] : 0;

if (isset($_POST['submit'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pickup_date = $_POST['pickup_date'] ?? '';
    $pickup_time = $_POST['pickup_time'] ?? '';
    $return_date = $_POST['return_date'] ?? '';
    $return_time = $_POST['return_time'] ?? '';
    $car_id = (int) ($_POST['car_id'] ?? 0);

    if (empty($fullname) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($pickup_date) || empty($pickup_time) || empty($return_date) || empty($return_time) || empty($car_id)) {
        $msg = "Please fill all fields properly!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Please enter a valid email address!";
    } else {
        $car_check = mysqli_query($connect, "SELECT * FROM cars WHERE id=" . $car_id);
        if (!$car_check || mysqli_num_rows($car_check) == 0) {
            $msg = "Please select a valid car!";
        } else {
            $car_row = mysqli_fetch_assoc($car_check);
            $car_type = $car_row['name'];

            $pickup_datetime = $pickup_date . " " . $pickup_time;
            $return_datetime = $return_date . " " . $return_time;

            if (strtotime($return_datetime) <= strtotime($pickup_datetime)) {
                $msg = "Return time must be after pickup time!";
            } else {
                // Total amount = number of days (rounded up) x the car's per-day price
                $seconds = strtotime($return_datetime) - strtotime($pickup_datetime);
                $days = max(1, (int) ceil($seconds / 86400));
                $total_amount = $days * (float) $car_row['price'];

                $fullname_esc = mysqli_real_escape_string($connect, $fullname);
                $email_esc = mysqli_real_escape_string($connect, $email);
                $phone_esc = mysqli_real_escape_string($connect, $phone);
                $address_esc = mysqli_real_escape_string($connect, $address);
                $city_esc = mysqli_real_escape_string($connect, $city);
                $car_type_esc = mysqli_real_escape_string($connect, $car_type);

                $user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
                $user_id_sql = $user_id ? $user_id : "NULL";

                $sql = "INSERT INTO bookings (user_id, car_id, fullname, email, phone, address, city, pickup_datetime, return_datetime, car_type, total_amount, payment_status, status)
                        VALUES ($user_id_sql, $car_id, '$fullname_esc', '$email_esc', '$phone_esc', '$address_esc', '$city_esc', '$pickup_datetime', '$return_datetime', '$car_type_esc', $total_amount, 'Unpaid', 'Pending')";

                if (mysqli_query($connect, $sql)) {
                    $booking_id = mysqli_insert_id($connect);
                    $order_id = payhere_generate_order_id($booking_id);
                    $order_id_esc = mysqli_real_escape_string($connect, $order_id);
                    mysqli_query($connect, "UPDATE bookings SET order_id='$order_id_esc' WHERE id=" . (int) $booking_id);

                    header("Location: payment.php?booking_id=" . $booking_id);
                    exit;
                } else {
                    $msg = "Error: " . mysqli_error($connect);
                }
            }
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

<section class="booking">
 <form class="booking-form" method="POST">
    <h2>Book Your Ride</h2>

    <?php if($msg != "") { echo "<p style='text-align:center;color:yellow'>$msg</p>"; } ?>

    <label>Full Name</label>
    <input type="text" name="fullname" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : (isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : ''); ?>">

    <label>Email Address</label>
    <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

    <label>Phone Number</label>
    <input type="tel" name="phone" placeholder="e.g. 0771234567" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

    <label>Address</label>
    <input type="text" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">

    <label>City</label>
    <input type="text" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">

    <label>Pickup Date</label>
    <input type="date" name="pickup_date" required>

    <label>Pickup Time</label>
    <input type="time" name="pickup_time" required>

    <label>Return Date</label>
    <input type="date" name="return_date" required>

    <label>Return Time</label>
    <input type="time" name="return_time" required>

    <label>Select Car</label>
    <select name="car_id" required>
      <option value="">Select a Car</option>
      <?php
      if(mysqli_num_rows($cars_result) > 0){
          while($car = mysqli_fetch_assoc($cars_result)){
              $selected = ($car['id'] == $preselect_car_id) ? 'selected' : '';
              echo '<option value="'.$car['id'].'" '.$selected.'>'.htmlspecialchars($car['name']).' - Rs.'.number_format($car['price'],2).'/Day</option>';
          }
      }
      ?>
    </select>

    <p style="text-align:center;color:#ccc;font-size:13px;">You'll be taken to a secure PayHere payment page after this step.</p>

    <button type="submit" name="submit">Proceed to Payment</button>

</form>

</section>

<footer>
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
