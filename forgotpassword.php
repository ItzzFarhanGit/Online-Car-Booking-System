<?php
include 'db.php';
session_start();

$msg = "";

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($connect, $_POST['email']);

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($connect, $sql);

    if(mysqli_num_rows($result) > 0){
        header("Location: reset.php?email=".$email);
        exit;
    } else {
        $msg = "Email not found!";
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

        <button type="submit" name="submit" class="btn">Send Reset Link</button>

        <p class="back-link">
            Remember Your Password? <a href="login.php">Login</a>
        </p>
    </form>
</div>

</body>
</html>
