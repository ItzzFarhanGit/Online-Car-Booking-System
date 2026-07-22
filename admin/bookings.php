<?php
session_start();
include 'db.php';
include 'auth_check.php';

$msg = "";

// Update booking status
if (isset($_POST['update_status'])) {
    $id = (int) $_POST['id'];
    $status = mysqli_real_escape_string($connect, $_POST['status']);
    $sql = "UPDATE bookings SET status='$status' WHERE id=$id";
    if (mysqli_query($connect, $sql)) {
        $msg = "Booking status updated!";
    } else {
        $msg = "Error: " . mysqli_error($connect);
    }
}

// Delete booking
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($connect, "DELETE FROM bookings WHERE id=$id");
    header("Location: bookings.php");
    exit;
}

$bookings_result = mysqli_query($connect, "SELECT * FROM bookings ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings | Admin</title>
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <h1>Manage Bookings</h1>

    <?php if ($msg != "") { echo "<p class='msg'>$msg</p>"; } ?>

    <section class="admin-table-box">
      <table class="admin-table">
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Email</th>
          <th>Pickup</th>
          <th>Return</th>
          <th>Car Type</th>
          <th>Amount</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($bookings_result) > 0): ?>
          <?php while ($b = mysqli_fetch_assoc($bookings_result)): ?>
            <tr>
              <td>#<?php echo $b['id']; ?></td>
              <td><?php echo htmlspecialchars($b['fullname']); ?></td>
              <td><?php echo htmlspecialchars($b['email']); ?></td>
              <td><?php echo htmlspecialchars($b['pickup_datetime']); ?></td>
              <td><?php echo htmlspecialchars($b['return_datetime']); ?></td>
              <td><?php echo htmlspecialchars($b['car_type']); ?></td>
              <td>Rs. <?php echo number_format((float) $b['total_amount'], 2); ?></td>
              <td>
                <?php
                  $payStatus = $b['payment_status'] ?? 'Unpaid';
                  $payColor = ['Paid' => '#1a7f37', 'Unpaid' => '#999', 'Pending' => '#f0ad4e', 'Failed' => '#d9534f', 'Cancelled' => '#d9534f'];
                  $color = $payColor[$payStatus] ?? '#999';
                ?>
                <span style="color:<?php echo $color; ?>;font-weight:bold;"><?php echo htmlspecialchars($payStatus); ?></span>
              </td>
              <td>
                <form method="POST" class="status-form">
                  <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                  <input type="hidden" name="update_status" value="1">
                  <select name="status" onchange="this.form.submit()">
                    <?php foreach (['Pending', 'Confirmed', 'Completed', 'Cancelled'] as $opt): ?>
                      <option value="<?php echo $opt; ?>" <?php echo ($b['status']==$opt) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
              <td>
                <a href="bookings.php?delete=<?php echo $b['id']; ?>" class="link-delete" onclick="return confirm('Delete this booking?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10">No bookings yet.</td></tr>
        <?php endif; ?>
      </table>
    </section>

  </main>
</div>

</body>
</html>
