<?php
session_start();
include 'db.php';
$msg = "";

// The email is only trusted here if it was verified via OTP in verify_otp.php.
// This prevents anyone from resetting a password just by guessing/typing an email in the URL.
if (!isset($_SESSION['reset_verified_email'])) {
    header("Location: forgotpassword.php");
    exit;
}
$email = $_SESSION['reset_verified_email'];

if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $msg = "Password must be at least 6 characters!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email_esc = mysqli_real_escape_string($connect, $email);
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email_esc'";
        $result = mysqli_query($connect, $sql);

        if ($result) {
            unset($_SESSION['reset_verified_email']);
            header("Location: login.php?reset=success");
            exit;
        } else {
            $msg = "Error! Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | Online Car Booking</title>
<link rel="Stylesheet" href="reset.css">
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <p style="text-align:center; color:#666; font-size:14px;">for <strong><?php echo htmlspecialchars($email); ?></strong></p>

    <?php if($msg != ""){ echo "<p class='error'>$msg</p>"; } ?>

    <form method="POST">
        <div class="input-box">
            <input type="password" name="new_password" placeholder="Enter New Password" required>
        </div>
        <div class="input-box">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        </div>
        <button type="submit" name="submit" class="btn">Reset Password</button>
        <p class="back-link">Remembered? <a href="login.php">Login</a></p>
        <p class="back-link"><a href="home.php">&larr; Back to Home</a></p>
    </form>
</div>
</body>
</html>
