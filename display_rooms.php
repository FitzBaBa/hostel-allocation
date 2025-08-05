<?php
$conn = mysqli_connect("localhost", "root", "", "camp_db");

$rooms = mysqli_query($conn, "SELECT * FROM hostel_rooms ORDER BY hostel_name, block, room_number");

echo "<style>
    table { border-collapse: collapse; width: 80%; margin: 20px auto; font-family: Arial; }
    th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: center; }
    th { background-color: rgb(51, 68, 99); color: white; }
    tr:nth-child(even) { background-color: #fafafa; }
    .full { color: red; font-weight: bold; }
    .available { color: green; font-weight: bold; }
</style>";

echo "<table>
    <tr><th>Hostel</th><th>Block</th><th>Room</th><th>Occupancy</th><th>Status</th></tr>";

while ($room = mysqli_fetch_assoc($rooms)) {
    $status = $room['capacity_used'] >= $room['capacity'] ? "Full" : "Available";
    $statusClass = $status === "Full" ? "full" : "available";

    echo "<tr>
        <td>{$room['hostel_name']}</td>
        <td>{$room['block']}</td>
        <td>{$room['room_number']}</td>
        <td>{$room['capacity_used']}/{$room['capacity']}</td>
        <td class='$statusClass'>$status</td>
    </tr>";
}

echo "</table>";
?>
