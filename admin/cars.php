<?php
session_start();
include 'db.php';
include 'auth_check.php';

$msg = "";

// Add new car
if (isset($_POST['add_car'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $image = mysqli_real_escape_string($connect, $_POST['image']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);

    if (empty($name) || empty($price) || empty($image)) {
        $msg = "Please fill all fields!";
    } else {
        $sql = "INSERT INTO cars (name, price, image, status) VALUES ('$name', '$price', '$image', '$status')";
        if (mysqli_query($connect, $sql)) {
            $msg = "Car added successfully!";
        } else {
            $msg = "Error: " . mysqli_error($connect);
        }
    }
}

// Update existing car
if (isset($_POST['update_car'])) {
    $id = (int) $_POST['id'];
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $image = mysqli_real_escape_string($connect, $_POST['image']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);

    $sql = "UPDATE cars SET name='$name', price='$price', image='$image', status='$status' WHERE id=$id";
    if (mysqli_query($connect, $sql)) {
        $msg = "Car updated successfully!";
    } else {
        $msg = "Error: " . mysqli_error($connect);
    }
}

// Delete car
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($connect, "DELETE FROM cars WHERE id=$id");
    header("Location: cars.php");
    exit;
}

// Get a car to edit (pre-fill form)
$edit_car = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = mysqli_query($connect, "SELECT * FROM cars WHERE id=$id");
    $edit_car = mysqli_fetch_assoc($res);
}

$cars_result = mysqli_query($connect, "SELECT * FROM cars ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Cars | Admin</title>
<link rel="Stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <h1>Manage Cars</h1>

    <?php if ($msg != "") { echo "<p class='msg'>$msg</p>"; } ?>

    <section class="admin-form-box">
      <h2><?php echo $edit_car ? 'Edit Car' : 'Add New Car'; ?></h2>
      <form method="POST" class="inline-form">
        <?php if ($edit_car) { ?>
          <input type="hidden" name="id" value="<?php echo $edit_car['id']; ?>">
        <?php } ?>

        <input type="text" name="name" placeholder="Car Name (e.g. Hyundai i20)" required
               value="<?php echo $edit_car ? htmlspecialchars($edit_car['name']) : ''; ?>">

        <input type="number" step="0.01" name="price" placeholder="Price per Day" required
               value="<?php echo $edit_car ? htmlspecialchars($edit_car['price']) : ''; ?>">

        <input type="text" name="image" placeholder="Image filename (inside IMAGES/ folder)" required
               value="<?php echo $edit_car ? htmlspecialchars($edit_car['image']) : ''; ?>">

        <select name="status">
          <option value="Available" <?php echo ($edit_car && $edit_car['status']=='Available') ? 'selected' : ''; ?>>Available</option>
          <option value="Unavailable" <?php echo ($edit_car && $edit_car['status']=='Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
        </select>

        <?php if ($edit_car) { ?>
          <button type="submit" name="update_car" class="btn">Update Car</button>
          <a href="cars.php" class="btn btn-secondary">Cancel</a>
        <?php } else { ?>
          <button type="submit" name="add_car" class="btn">Add Car</button>
        <?php } ?>
      </form>
    </section>

    <section class="admin-table-box">
      <h2>All Cars</h2>
      <table class="admin-table">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Price/Day</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($cars_result) > 0): ?>
          <?php while ($car = mysqli_fetch_assoc($cars_result)): ?>
            <tr>
              <td><?php echo $car['id']; ?></td>
              <td><img src="../IMAGES/<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="thumb"></td>
              <td><?php echo htmlspecialchars($car['name']); ?></td>
              <td>Rs.<?php echo number_format($car['price'], 2); ?></td>
              <td><span class="badge badge-<?php echo strtolower($car['status']); ?>"><?php echo htmlspecialchars($car['status']); ?></span></td>
              <td>
                <a href="cars.php?edit=<?php echo $car['id']; ?>" class="link-edit">Edit</a>
                <a href="cars.php?delete=<?php echo $car['id']; ?>" class="link-delete" onclick="return confirm('Delete this car?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No cars added yet.</td></tr>
        <?php endif; ?>
      </table>
    </section>

  </main>
</div>

</body>
</html>
