<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$msg = "";

if (isset($_POST['submit'])) {
    $email = trim($_POST['email'] ?? '');
    if ($email !== '') {
        $email_esc = mysqli_real_escape_string($connect, $email);
        $sql = "SELECT * FROM users WHERE email='$email_esc' AND is_verified=1";
        $result = mysqli_query($connect, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $otpResult = generate_and_send_otp($connect, $email, $user['fullname'], 'reset');

            if ($otpResult['success']) {
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp_purpose'] = 'reset';
                $_SESSION['otp_last_sent'] = time();
                header("Location: verify_otp.php");
                exit;
            } else {
                $msg = "Could not send the reset code (" . htmlspecialchars($otpResult['error']) . "). Please check the SMTP settings in config.php.";
            }
        } else {
            // Deliberately vague message: don't reveal whether the email
            // exists in the system, to avoid leaking which emails are registered.
            $msg = "If that email is registered, a verification code has been sent.";
        }
    } else {
        $msg = "Please enter your email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | Online Car Booking</title>
<link rel="STylesheet" href="forgotpassword.css">
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>

    <?php if($msg != ""){ echo "<p class='error'>$msg</p>"; } ?>

    <form method="POST">
        <div class="input-box">
            <input type="email" name="email" placeholder="Enter Your Registered Email" required>
        </div>

        <button type="submit" name="submit" class="btn">Send Verification Code</button>

        <p class="back-link">
            Remember Your Password? <a href="login.php">Login</a>
        </p>
        <p class="back-link"><a href="home.php">&larr; Back to Home</a></p>
    </form>
</div>

</body>
</html>
