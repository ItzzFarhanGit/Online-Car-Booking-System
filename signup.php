<?php
include 'db.php';

$msg = "";

if(isset($_POST['submit'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match
    if($password != $confirm_password){
        $msg = "Passwords do not match!";
    } else {

        // Secure password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert query
        $sql = "INSERT INTO users(fullname, email, username, password) 
                VALUES ('$fullname', '$email', '$username', '$hashed_password')";

        if(mysqli_query($connect, $sql)){   // <-- CORRECT VARIABLE
            header("Location: login.php");
            exit;
        } else {
            $msg = "Error: Unable to register!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup | Online Car Booking</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> 
  <link rel="Stylesheet" href="signup.css">
</head>
<body>
  <div class="container">
    <div class="signup-box">
      <h2>Create Account</h2>

      <?php if($msg != ""){ echo "<p class='error'>$msg</p>"; } ?>

      <form method="POST">
        <div class="input-box">
          <input type="text" name="fullname" required>
          <label>Full Name</label>
        </div>

        <div class="input-box">
          <input type="email" name="email" required>
          <label>Email</label>
        </div>

        <div class="input-box">
          <input type="text" name="username" required>
          <label>Username</label>
        </div>

        <div class="input-box">
          <input type="password" name="password" required>
          <label>Password</label>
        </div>

        <div class="input-box">
          <input type="password" name="confirm_password" required>
          <label>Confirm Password</label>
        </div>

        <button type="submit" name="submit" class="btn">Sign Up</button>
        <p class="login-link">Already have an Account? <a href="login.php">Login</a></p>
      </form>

    </div>
  </div>
</body>
</html>
