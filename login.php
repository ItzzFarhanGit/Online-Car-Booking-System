<?php
include 'db.php';
session_start();

$msg = "";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user exists by username
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($connect, $sql);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);

        // Verify hashed password
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            header("Location: home.php");
            exit;
        } else {
            $msg = "Invalid Username or Password!";
        }
    } else {
        $msg = "Invalid Username or Password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Online Car Booking</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="Stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <h2>Login</h2>

      <?php if($msg != ""){ echo "<p class='error'>$msg</p>"; } ?>

      <form method="POST">
        <div class="input-box">
          <input type="text" name="username" required>
          <label>Username</label>
        </div>
        <div class="input-box">
          <input type="password" name="password" required>
          <label>Password</label>
        </div>
        <a href="forgotpassword.php" class="forgot">Forgot Password?</a>

        <button type="submit" name="login" class="btn">Login</button>
        <p class="signup-link">Don’t have an Account? <a href="signup.php">Sign Up</a></p>
      </form>
    </div>
  </div>
</body>
</html>
