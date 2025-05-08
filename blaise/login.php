<?php
session_start();
require 'db_connect.php'; // Ensure this file correctly establishes a database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        echo "<script>alert('Both fields are required.'); window.location.href='login.html';</script>";
        exit();
    }
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id, password FROM userss WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            echo "<script>window.location.href='Task2.html';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='login.html';</script>";
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}