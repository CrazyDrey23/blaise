<?php
session_start();
require 'db_connect.php'; // Ensure this file correctly establishes a database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: sign_up.html");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: sign_up.html");
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: sign_up.html");
        exit();
    }
    
    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM userss WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email is already registered.');</script>";
        $stmt->close();
        header("Location: sign_up.html");
        exit();
    }
    $stmt->close();
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO userss (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful. You can now log in.');</script>";
        header("Location: Task2.html");
    } else {
        echo "<script>alert('Registration failed. Please try again.');</script>";
        header("Location: sign_up.html");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: sign_up.html");
    exit();
}
