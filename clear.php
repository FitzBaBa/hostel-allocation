<?php
$conn = mysqli_connect("localhost", "root", "", "allocation_db");

// Completely reset the table
mysqli_query($conn, "TRUNCATE TABLE rooms");
echo "Table cleared.";
?>
