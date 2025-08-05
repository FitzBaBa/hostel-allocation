<?php
include 'allocate.php';

// Connect to DB
$conn = mysqli_connect("localhost", "root", "", "allocation_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize result message
$result = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $sessions = $_POST['sessions'] ?? [];

    if (count($sessions) === 4) {
        // All sessions paid – allocate to regular room
        $result = allocateStudent($name, $conn);
    } else {
        // Not fully paid – send to AWAITING-PAYMENT room
        $awaitingRoomQuery = "SELECT * FROM rooms WHERE room_number = 'AWAITING-PAYMENT' LIMIT 1";
        $res = mysqli_query($conn, $awaitingRoomQuery);

        if ($room = mysqli_fetch_assoc($res)) {
            $room_id = $room['id'];
            $stmt = $conn->prepare("INSERT INTO students (name, room_id) VALUES (?, ?)");
            if (!$stmt) {
                $result = "❌ Failed to prepare insert: " . $conn->error;
            } else {
                $stmt->bind_param("si", $name, $room_id);
                if ($stmt->execute()) {
                    $result = "⏳ $name sent to AWAITING PAYMENT room for follow-up.";
                } else {
                    $result = "❌ Insert failed: " . $stmt->error;
                }
            }
        } else {
            $result = "⚠️ 'AWAITING-PAYMENT' room not found. Please create it.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Allocate Student</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <aside class="sidebar">
    <h2>School Dashboard</h2>
    <nav>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_student.php" class="active">Allocate Student</a></li>
        <li><a href="pending_payment.php">Pending Payment</a></li>
        <li><a href="complaints.php">Complaints</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header>
      <h1>Allocate Student to Room</h1>
    </header>

    <section class="allocations">
      <?php if ($result): ?>
          <div class="message"><?= htmlspecialchars($result) ?></div>
      <?php endif; ?>

      <form method="post">
          <label for="name">Student Name:</label><br>
          <input type="text" name="name" id="name" required><br><br>

          <p>Confirm Payment for Sessions:</p>
          <label><input type="checkbox" name="sessions[]" value="1"> Year 1</label><br>
          <label><input type="checkbox" name="sessions[]" value="2"> Year 2</label><br>
          <label><input type="checkbox" name="sessions[]" value="3"> Year 3</label><br>
          <label><input type="checkbox" name="sessions[]" value="4"> Year 4</label><br><br>

          <button type="submit">Allocate</button>
      </form>
    </section>
  </main>
</body>
</html>
