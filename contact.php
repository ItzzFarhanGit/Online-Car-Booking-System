<?php
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

  <div class="contact-card">
    <h2>Contact Us</h2>

    <?php if($msg != ""){ echo "<p class='msg'>$msg</p>"; } ?>

    <form action="" method="post">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Enter Your Full Name" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter Your Email" required>
      </div>

      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" placeholder="Enter Subject" required>
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" placeholder="Write Your Message Here" required></textarea>
      </div>

      <button type="submit" class="btn">Send Message</button>
    
      <?php if($msg != ""){ echo "<p class='msg'>$msg</p>"; } ?>
 

    </form>
  </div>

</body>
</html>
