<?php
$servername = "localhost"; // XAMPP runs MySQL on localhost
$username = "root"; // Default MySQL username
$password = ""; // Default MySQL password (leave blank in XAMPP)
$dbname = "user_system"; // Database name

// Create connection
$conn = new mysqli('localhost', 'root', '', 'user_system');


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

