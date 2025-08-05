<?php
$conn = mysqli_connect("localhost", "root", "", "allocation_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle complaint resolution
if (isset($_POST['resolve_id'])) {
    $id = intval($_POST['resolve_id']);
    mysqli_query($conn, "UPDATE complaints SET status='resolved' WHERE id=$id");
}

// Fetch complaints
$complaints = mysqli_query($conn, "SELECT c.*, s.name 
                                   FROM complaints c 
                                   JOIN students s ON c.student_id = s.id 
                                   ORDER BY c.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Complaints</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <aside class="sidebar">
    <h2>School Dashboard</h2>
    <nav>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_student.php">Allocate Student</a></li>
        <li><a href="pending_payment.php">Pending Payment</a></li>
        <li><a href="complaints.php" class="active">Complaints</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header><h1>Student Complaints</h1></header>
    <section class="allocations">
      <table>
        <tr>
          <th>Student</th>
          <th>Complaint</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($complaints)): ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['complaint_text']) ?></td>
            <td><?= $row['status'] ?></td>
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="resolve_id" value="<?= $row['id'] ?>">
                  <button type="submit">Resolve</button>
                </form>
              <?php else: ?>
                Resolved
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </section>
  </main>
</body>
</html>
