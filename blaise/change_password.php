<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // Validate password
    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords don't match!";
        header("Location: change_password.php");
        exit();
    }
    
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || 
        !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $_SESSION['error'] = "Password must contain uppercase, lowercase, and numbers!";
        header("Location: change_password.php");
        exit();
    }
    
    // Update password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $conn->query("UPDATE users SET 
        password = '$hash',
        login_attempts = 0,
        locked_until = NULL
        WHERE email = '{$_SESSION['reset_email']}'");
    
    unset($_SESSION['reset_email']);
    $_SESSION['success'] = "Password reset successfully!";
    header("Location: login.php");
    exit();
}