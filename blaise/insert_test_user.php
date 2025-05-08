<?php
include 'db_connect.php'; // Ensure db_connect.php correctly connects to your database

$fullname = "John Doe";
$email = "johndoe@example.com";
$password = password_hash("mypassword", PASSWORD_DEFAULT); // Hash password for security

$sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $fullname, $email, $password);

if ($stmt->execute()) {
    echo "Test user added!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
