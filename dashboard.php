<?php
$conn = mysqli_connect("localhost", "root", "", "allocation_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Allocation Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <aside class="sidebar">
    <h2>School Dashboard</h2>
    <nav>
      <ul>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="add_student.php">Allocate Student</a></li>
        <li><a href="pending_payment.php">Pending Payment</a></li>
        <li><a href="complaints.php">Complaints</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header>
      <h1>Welcome, Admin</h1>
    </header>

    <section class="allocations">
      <h2>Room Status</h2>
      <?php include 'display_rooms.php'; ?>
    </section>
  </main>
</body>
</html>
