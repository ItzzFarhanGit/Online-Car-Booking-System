<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logged Out | Online Car Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="Stylesheet" href="logout.css">
</head>
<body>

<div class="logout-box">
    <h2>Logged Out Successfully!</h2>
    <p>You have been logged out of your account.</p>
    <a href="signup.php">Go to Sign Up</a>
</div>

</body>
</html>
