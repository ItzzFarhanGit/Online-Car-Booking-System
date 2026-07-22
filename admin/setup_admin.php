<?php
/*
  ONE-TIME SETUP SCRIPT
  ----------------------
  Run this once in your browser (e.g. http://localhost/Online-Car-Booking-System/admin/setup_admin.php)
  to create your first Admin Panel login. It uses PHP's password_hash() directly,
  so there is no risk of a mismatched/incorrect password hash.

  IMPORTANT: Delete this file (or rename it) after you have created your admin
  account, so nobody else can use it to create more admin logins.
*/

include 'db.php';

$msg = "";
$done = false;

// Stop this script from running if an admin already exists
$check = mysqli_query($connect, "SELECT COUNT(*) AS total FROM admin");
$row = mysqli_fetch_assoc($check);

if ($row['total'] > 0) {
    $msg = "An admin account already exists. For security, this setup page is disabled. Delete setup_admin.php from your server.";
} else {
    if (isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($username) || empty($password) || empty($confirm_password)) {
            $msg = "Please fill all fields!";
        } elseif ($password !== $confirm_password) {
            $msg = "Passwords do not match!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $username_esc = mysqli_real_escape_string($connect, $username);

            $sql = "INSERT INTO admin (username, password) VALUES ('$username_esc', '$hashed_password')";
            if (mysqli_query($connect, $sql)) {
                $done = true;
                $msg = "Admin account created successfully! You can now delete setup_admin.php and login at admin/login.php.";
            } else {
                $msg = "Error: " . mysqli_error($connect);
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
<title>Admin Setup | Online Car Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<header class="site-header">
  <div class="logo">Online Car Booking</div>
  <nav>
    <ul>
      <li><a href="../home.php">Home</a></li>
    </ul>
  </nav>
</header>

<section class="admin-hero">
  <div class="admin-login-box">
    <h2>Create First Admin Account</h2>

    <?php if ($msg != "") { echo "<p class='msg'>$msg</p>"; } ?>

    <?php if (!$done && $row['total'] == 0) { ?>
    <form method="POST">
      <div class="input-box">
        <input type="text" name="username" placeholder="Admin Username" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="input-box">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </div>
      <button type="submit" name="submit" class="btn">Create Admin</button>
    </form>
    <?php } else { ?>
      <p><a href="login.php">Go to Admin Login</a></p>
    <?php } ?>
  </div>
</section>

<footer class="site-footer">
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
