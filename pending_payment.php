<?php
include 'allocate.php';
$conn = mysqli_connect("localhost", "root", "", "allocation_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $res = mysqli_query($conn, "SELECT name FROM students WHERE id = $student_id");

    if ($student = mysqli_fetch_assoc($res)) {
        $name = $student['name'];

        // RANDOMIZED room selection
        $roomRes = mysqli_query($conn, "SELECT * FROM rooms WHERE capacity_used < capacity ORDER BY RAND() LIMIT 1");

        if ($room = mysqli_fetch_assoc($roomRes)) {
            $new_room_id = $room['id'];

            // Update student's room_id
            $stmt = $conn->prepare("UPDATE students SET room_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_room_id, $student_id);

            if ($stmt->execute()) {
                // Update room capacity
                mysqli_query($conn, "UPDATE rooms SET capacity_used = capacity_used + 1 WHERE id = $new_room_id");

                $result = "✅ $name has been randomly allocated to {$room['hostel']} Block {$room['block']} Room {$room['room_number']}";
            } else {
                $result = "❌ Failed to update student record: " . $stmt->error;
            }
        } else {
            $result = "❌ No available rooms found!";
        }
    } else {
        $result = "❌ Student not found!";
    }
}



$awaitingRoomRes = mysqli_query($conn, "SELECT id FROM rooms WHERE room_number = 'AWAITING-PAYMENT'");
$awaitingRoom = mysqli_fetch_assoc($awaitingRoomRes);
$awaitingRoomId = $awaitingRoom['id'] ?? 0;

$students = mysqli_query($conn, "SELECT * FROM students WHERE room_id = $awaitingRoomId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Payment</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <aside class="sidebar">
    <h2>School Dashboard</h2>
    <nav>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_student.php">Allocate Student</a></li>
        <li><a href="pending_payment.php" class="active">Pending Payment</a></li>
        <li><a href="complaints.php">Complaints</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header>
      <h1>Pending Payment Dashboard</h1>
    </header>

    <section class="allocations">
      <?php if ($result): ?>
          <div class="message"><?= htmlspecialchars($result) ?></div>
      <?php endif; ?>

      <table>
          <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Action</th>
          </tr>
          <?php while ($student = mysqli_fetch_assoc($students)) : ?>
              <tr>
                  <td><?= $student['id'] ?></td>
                  <td><?= htmlspecialchars($student['name']) ?></td>
                  <td>
                      <form method="post">
                          <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                          <button type="submit">✅ Mark as Paid & Allocate</button>
                      </form>
                  </td>
              </tr>
          <?php endwhile; ?>
      </table>
    </section>
  </main>
</body>
</html>
