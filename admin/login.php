<?php
session_start();
include 'db.php';

$msg = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username_esc = mysqli_real_escape_string($connect, $username);
    $sql = "SELECT * FROM admin WHERE username='$username_esc'";
    $result = mysqli_query($connect, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];

            header("Location: dashboard.php");
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
<title>Admin Login | Online Car Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<header class="site-header">
  <div class="logo">Online Car Booking</div>
  <nav>
    <ul>
      <li><a href="../home.php">Home</a></li>
      <li><a href="../caravailable.php">Cars</a></li>
      <li><a href="../login.php">User Login</a></li>
    </ul>
  </nav>
</header>

<section class="admin-hero">
  <div class="admin-login-box">
    <h2>Admin Login</h2>

    <?php if ($msg != "") { echo "<p class='error'>$msg</p>"; } ?>

    <form method="POST">
      <div class="input-box">
        <input type="text" name="username" placeholder="Admin Username" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" name="login" class="btn">Login</button>
      <p class="back-link"><a href="../home.php">&larr; Back to Home</a></p>
    </form>
  </div>
</section>

<footer class="site-footer">
  <p>© 2025 Online Car Booking System | Designed by <strong>Mohammed Farhan</strong></p>
</footer>

</body>
</html>
