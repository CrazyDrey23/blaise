<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['mfa_email'])) {
    header("Location: login.php");
    exit();
}

$user = $conn->query("SELECT * FROM users WHERE email = '{$_SESSION['mfa_email']}'")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer = strtolower(trim($_POST['answer']));
    
    if (strtolower(trim($user['security_answer'])) === $answer) {
        $_SESSION['reset_email'] = $_SESSION['mfa_email'];
        unset($_SESSION['mfa_email']);
        header("Location: change_password.php");
    } else {
        $_SESSION['error'] = "Incorrect answer!";
        header("Location: security_question.php");
    }
    exit();
}