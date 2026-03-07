<?php
include 'db.php';
$msg = "";

// Get email from URL
if(isset($_GET['email'])){
    $email = $_GET['email'];
} else {
    header("Location: forgot.php");
    exit;
}

// Handle form submission
if(isset($_POST['submit'])){
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password != $confirm_password){
        $msg = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in DB
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        $result = mysqli_query($connect, $sql);

        if($result){
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
    </form>
</div>
</body>
</html>
