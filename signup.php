<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$msg = "";

if (isset($_POST['submit'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } elseif ($fullname === '' || $email === '' || $username === '' || $password === '') {
        $msg = "Please fill all fields properly!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Please enter a valid email address!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $fullname_esc = mysqli_real_escape_string($connect, $fullname);
        $email_esc = mysqli_real_escape_string($connect, $email);
        $username_esc = mysqli_real_escape_string($connect, $username);

        // If an unverified account with this email/username already exists,
        // remove it first so the person can retry signing up cleanly.
        mysqli_query($connect, "DELETE FROM users WHERE (email='$email_esc' OR username='$username_esc') AND is_verified=0");

        $sql = "INSERT INTO users(fullname, email, username, password, is_verified) VALUES ('$fullname_esc', '$email_esc', '$username_esc', '$hashed_password', 0)";

        if (mysqli_query($connect, $sql)) {
            $new_user_id = mysqli_insert_id($connect);
            $otpResult = generate_and_send_otp($connect, $email, $fullname, 'signup');

            if ($otpResult['success']) {
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp_purpose'] = 'signup';
                $_SESSION['otp_user_id'] = $new_user_id;
                $_SESSION['otp_last_sent'] = time();
                header("Location: verify_otp.php");
                exit;
            } else {
                // Roll back the just-created account if the email couldn't be sent,
                // e.g. because config.php SMTP credentials haven't been filled in yet.
                mysqli_query($connect, "DELETE FROM users WHERE user_id=" . (int) $new_user_id);
                $msg = "Account created, but we couldn't send the verification email (" . htmlspecialchars($otpResult['error']) . "). Please check the SMTP settings in config.php and try again.";
            }
        } else {
            if (mysqli_errno($connect) == 1062) {
                $msg = "That username or email is already registered!";
            } else {
                $msg = "Error: Unable to register!";
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
          <input type="text" name="fullname" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
          <label>Full Name</label>
        </div>

        <div class="input-box">
          <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
          <label>Email</label>
        </div>

        <div class="input-box">
          <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
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
        <p class="login-link"><a href="home.php">&larr; Back to Home</a></p>
      </form>

    </div>
  </div>
</body>
</html>
