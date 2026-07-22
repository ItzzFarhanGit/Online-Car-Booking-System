<?php
session_start();
include 'db.php';

$msg = "";     

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $name    = mysqli_real_escape_string($connect, $_POST['name']);
    $email   = mysqli_real_escape_string($connect, $_POST['email']);
    $subject = mysqli_real_escape_string($connect, $_POST['subject']);
    $message = mysqli_real_escape_string($connect, $_POST['message']);

    if(empty($name) || empty($email) || empty($subject) || empty($message)){
        $msg = "Please fill all fields properly!";
    } else {
        $sql = "INSERT INTO contact(name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if(mysqli_query($connect, $sql)){
            $msg = "✅ Your message has been sent successfully!";
        } else {
            $msg = "❌ Failed to send message! Error: " . mysqli_error($connect);
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Online Car Booking</title>
  <link rel="Stylesheet" href="contact.css">
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
        <li><a href="contact.php" class="active">CONTACT</a></li>
        <?php if(isset($_SESSION['username'])): ?>
          <li><a href="mybookings.php">MY BOOKINGS</a></li>
          <li><a href="logout.php" class="btn-login">LOGOUT</a></li>
        <?php else: ?>
          <li><a href="login.php" class="btn-login">LOGIN</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <div class="contact-card">
    <h2>Contact Us</h2>

    <?php if($msg != ""){ echo "<p class='msg'>$msg</p>"; } ?>

    <form action="" method="post">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Enter Your Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter Your Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>

      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" placeholder="Enter Subject" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" placeholder="Write Your Message Here" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
      </div>

      <button type="submit" class="btn">Send Message</button>
    </form>
  </div>

</body>
</html>
