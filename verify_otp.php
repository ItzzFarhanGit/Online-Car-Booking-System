<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$purpose = $_SESSION['otp_purpose'] ?? '';
$email = $_SESSION['otp_email'] ?? '';

if ($email === '' || !in_array($purpose, ['signup', 'reset'], true)) {
    header("Location: login.php");
    exit;
}

$msg = "";
$info = "";

// Handle "Resend Code"
if (isset($_POST['resend'])) {
    $lastSent = $_SESSION['otp_last_sent'] ?? 0;
    $waitLeft = OTP_RESEND_COOLDOWN - (time() - $lastSent);

    if ($waitLeft > 0) {
        $msg = "Please wait $waitLeft seconds before requesting a new code.";
    } else {
        $nameForEmail = $purpose === 'signup' ? ($_SESSION['otp_fullname'] ?? 'there') : 'there';
        $otpResult = generate_and_send_otp($connect, $email, $nameForEmail, $purpose);
        if ($otpResult['success']) {
            $_SESSION['otp_last_sent'] = time();
            $info = "A new code has been sent to $email.";
        } else {
            $msg = "Could not resend the code: " . htmlspecialchars($otpResult['error']);
        }
    }
}

// Handle code verification
if (isset($_POST['verify'])) {
    $code = trim($_POST['otp_code'] ?? '');

    if ($code === '') {
        $msg = "Please enter the 6-digit code.";
    } else {
        $result = verify_otp($connect, $email, $purpose, $code);

        if ($result['success']) {
            if ($purpose === 'signup') {
                $user_id = (int) ($_SESSION['otp_user_id'] ?? 0);
                mysqli_query($connect, "UPDATE users SET is_verified=1 WHERE user_id=$user_id");

                $userRes = mysqli_query($connect, "SELECT * FROM users WHERE user_id=$user_id");
                $user = $userRes ? mysqli_fetch_assoc($userRes) : null;

                unset($_SESSION['otp_email'], $_SESSION['otp_purpose'], $_SESSION['otp_user_id'], $_SESSION['otp_last_sent'], $_SESSION['otp_fullname']);

                if ($user) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                }

                header("Location: home.php");
                exit;
            } else { // reset
                $_SESSION['reset_verified_email'] = $email;
                unset($_SESSION['otp_email'], $_SESSION['otp_purpose'], $_SESSION['otp_last_sent']);
                header("Location: reset.php");
                exit;
            }
        } else {
            $msg = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Code | Online Car Booking</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="Stylesheet" href="signup.css">
  <style>
    .otp-input { letter-spacing: 10px; font-size: 22px; text-align: center; }
    .resend-row { display:flex; justify-content:space-between; align-items:center; margin-top:10px; }
    .resend-btn { background:none; border:none; color:#0b5ed7; cursor:pointer; text-decoration:underline; font-size:14px; padding:0; }
    .info { color:#0b5ed7; font-size:14px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="signup-box">
      <h2>Verify Your Email</h2>
      <p style="text-align:center; color:#666; font-size:14px;">
        We sent a 6-digit code to <strong><?php echo htmlspecialchars($email); ?></strong>
      </p>

      <?php if ($msg != "") { echo "<p class='error'>" . htmlspecialchars($msg) . "</p>"; } ?>
      <?php if ($info != "") { echo "<p class='info'>" . htmlspecialchars($info) . "</p>"; } ?>

      <form method="POST">
        <div class="input-box">
          <input class="otp-input" type="text" name="otp_code" maxlength="6" inputmode="numeric" pattern="[0-9]*" required autofocus>
          <label>6-Digit Code</label>
        </div>

        <button type="submit" name="verify" class="btn">Verify</button>

        <div class="resend-row">
          <span></span>
          <button type="submit" name="resend" class="resend-btn">Resend Code</button>
        </div>
      </form>
      <p style="text-align:center; margin-top:14px;"><a href="home.php" style="color:#0b5ed7; text-decoration:none; font-size:14px;">&larr; Back to Home</a></p>
    </div>
  </div>
</body>
</html>
