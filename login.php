<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$msg = "";

if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $msg = "success:Password reset successful! Please login with your new password.";
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $username_esc = mysqli_real_escape_string($connect, $username);
        $sql = "SELECT * FROM users WHERE username='$username_esc'";
        $result = mysqli_query($connect, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                if ((int) $row['is_verified'] === 0) {
                    // Account exists but email was never verified - send a fresh
                    // OTP and route them to finish verification instead of logging in.
                    $otpResult = generate_and_send_otp($connect, $row['email'], $row['fullname'], 'signup');
                    if ($otpResult['success']) {
                        $_SESSION['otp_email'] = $row['email'];
                        $_SESSION['otp_purpose'] = 'signup';
                        $_SESSION['otp_user_id'] = $row['user_id'];
                        $_SESSION['otp_last_sent'] = time();
                        header("Location: verify_otp.php");
                        exit;
                    } else {
                        $msg = "Your email isn't verified yet, and we couldn't send a new code (" . htmlspecialchars($otpResult['error']) . ").";
                    }
                } else {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['fullname'] = $row['fullname'];

                    header("Location: home.php");
                    exit;
                }
            } else {
                $msg = "Invalid Username or Password!";
            }
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

      <?php
      if ($msg != "") {
          if (strpos($msg, 'success:') === 0) {
              echo "<p class='error' style='color:#1a7f37;'>" . htmlspecialchars(substr($msg, 8)) . "</p>";
          } else {
              echo "<p class='error'>" . htmlspecialchars($msg) . "</p>";
          }
      }
      ?>

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
        <p class="signup-link"><a href="home.php">&larr; Back to Home</a></p>
        <p class="signup-link" style="font-size:12px; opacity:0.8;">Admin? <a href="admin/login.php">Admin Login</a></p>
      </form>
    </div>
  </div>
</body>
</html>
