<?php
$conn = mysqli_connect("localhost", "root", "", "allocation_db");


$hostels = [
    'PTDF' => ['A', 'B'],
    'Asorock' => ['C', 'D'],
    'Kokori'=> ['E','F'],
];

foreach ($hostels as $hostel => $blocks) {
    foreach ($blocks as $block) {
        for ($room = 1; $room <= 3; $room++) {
            // Use a unique string for room_number
            $room_number = "$hostel-$block-$room";

            $query = "INSERT INTO rooms (room_number, hostel, block, capacity, capacity_used, status)
                      VALUES ('$room_number', '$hostel', '$block', 4, 0, 'available')";

            // Check for errors
            if (!mysqli_query($conn, $query)) {
                echo "Error inserting $room_number: " . mysqli_error($conn) . "<br>";
            }
        }
    }
}

echo "✅ Rooms inserted successfully.";
?>
