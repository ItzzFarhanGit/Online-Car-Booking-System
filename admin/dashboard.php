<?php
session_start();
include 'db.php';
include 'auth_check.php';

// Quick stats pulled live from the database
$cars_count     = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM cars"))['total'];
$bookings_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM bookings"))['total'];
$users_count    = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM users"))['total'];
$messages_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM contact"))['total'];
$pending_count  = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM bookings WHERE status='Pending'"))['total'];
$revenue_total  = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(total_amount),0) AS total FROM bookings WHERE payment_status='Paid'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Online Car Booking</title>
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <h1>Dashboard</h1>
    <p class="welcome">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>.</p>

    <div class="stat-grid">
      <div class="stat-card">
        <h3><?php echo $cars_count; ?></h3>
        <p>Total Cars</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $bookings_count; ?></h3>
        <p>Total Bookings</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $pending_count; ?></h3>
        <p>Pending Bookings</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $users_count; ?></h3>
        <p>Registered Users</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $messages_count; ?></h3>
        <p>Contact Messages</p>
      </div>
      <div class="stat-card">
        <h3>Rs. <?php echo number_format((float) $revenue_total, 2); ?></h3>
        <p>Revenue (Paid Bookings)</p>
      </div>
    </div>

    <div class="quick-links">
      <a href="cars.php" class="btn">Manage Cars</a>
      <a href="bookings.php" class="btn">Manage Bookings</a>
      <a href="messages.php" class="btn">View Messages</a>
    </div>
  </main>

</div>

</body>
</html>
