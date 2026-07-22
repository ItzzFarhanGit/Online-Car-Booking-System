<?php
session_start();
include 'db.php';
include 'auth_check.php';

// Delete message
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($connect, "DELETE FROM contact WHERE id=$id");
    header("Location: messages.php");
    exit;
}

$messages_result = mysqli_query($connect, "SELECT * FROM contact ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Messages | Admin</title>
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <h1>Contact Messages</h1>

    <section class="admin-table-box">
      <table class="admin-table">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Received</th>
          <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($messages_result) > 0): ?>
          <?php while ($m = mysqli_fetch_assoc($messages_result)): ?>
            <tr>
              <td><?php echo htmlspecialchars($m['name']); ?></td>
              <td><?php echo htmlspecialchars($m['email']); ?></td>
              <td><?php echo htmlspecialchars($m['subject']); ?></td>
              <td><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
              <td><?php echo htmlspecialchars($m['created_at']); ?></td>
              <td>
                <a href="messages.php?delete=<?php echo $m['id']; ?>" class="link-delete" onclick="return confirm('Delete this message?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No messages yet.</td></tr>
        <?php endif; ?>
      </table>
    </section>

  </main>
</div>

</body>
</html>
