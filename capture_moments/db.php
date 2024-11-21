<?php
// Database connection settings
$host = 'localhost';
$db = 'capture_moments'; // Your updated database name
$user = 'root'; // Your database username
$pass = ''; // Your database password (leave empty if not set)

// Create a new connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
