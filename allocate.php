<?php
function allocateStudent($name, $conn) {
    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Get a random available room
        $query = "SELECT * FROM rooms WHERE capacity_used < capacity ORDER BY RAND() LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (!$result || !($room = mysqli_fetch_assoc($result))) {
            throw new Exception("No available rooms!");
        }

        $room_id = $room['id'];

        // Step 2: Insert student
        $stmt = $conn->prepare("INSERT INTO students (name, room_id) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception("Student insert failed: " . $conn->error);
        }
        $stmt->bind_param("si", $name, $room_id);
        $stmt->execute();

        // Step 3: Update room occupancy
        $update = mysqli_query($conn, "UPDATE rooms SET capacity_used = capacity_used + 1 WHERE id = $room_id");
        if (!$update) {
            throw new Exception("Failed to update room occupancy: " . mysqli_error($conn));
        }

        // (Optional) If room becomes full, update status
        if ($room['capacity_used'] + 1 >= $room['capacity']) {
            mysqli_query($conn, "UPDATE rooms SET status = 'full' WHERE id = $room_id");
        }

        // Commit all changes
        mysqli_commit($conn);
        return "✅ Allocated $name to {$room['hostel']} Block {$room['block']} Room {$room['room_number']}";
    } catch (Exception $e) {
        // Rollback on failure
        mysqli_rollback($conn);
        return "❌ Allocation failed: " . $e->getMessage();
    }
}
?>
