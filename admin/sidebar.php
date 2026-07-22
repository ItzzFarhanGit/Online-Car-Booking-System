<?php
// Shared admin sidebar. Included by every admin page after session_start() + auth_check.
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
  <div class="admin-logo">Admin Panel</div>
  <nav>
    <ul>
      <li><a href="dashboard.php" class="<?php echo $current=='dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
      <li><a href="cars.php" class="<?php echo $current=='cars.php' ? 'active' : ''; ?>">Manage Cars</a></li>
      <li><a href="bookings.php" class="<?php echo $current=='bookings.php' ? 'active' : ''; ?>">Manage Bookings</a></li>
      <li><a href="messages.php" class="<?php echo $current=='messages.php' ? 'active' : ''; ?>">Contact Messages</a></li>
      <li><a href="../home.php">View Site</a></li>
      <li><a href="logout.php" class="btn-logout">Logout</a></li>
    </ul>
  </nav>
</aside>
