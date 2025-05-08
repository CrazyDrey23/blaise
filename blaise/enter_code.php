<?php
session_start();
require 'db_connect.php';

const MAX_MFA_ATTEMPTS = 5;

if (!isset($_SESSION['mfa_email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $user = $conn->query("SELECT * FROM users WHERE email = '{$_SESSION['mfa_email']}'")->fetch_assoc();

    if ($user['mfa_code'] === $code && strtotime($user['mfa_expiry']) > time()) {
        // Successful verification
        $_SESSION['reset_email'] = $_SESSION['mfa_email'];
        unset($_SESSION['mfa_email']);
        header("Location: change_password.php");
    } else {
        $_SESSION['mfa_attempts']++;
        
        if ($_SESSION['mfa_attempts'] >= MAX_MFA_ATTEMPTS) {
            // Fallback to security question
            header("Location: security_question.php");
        } else {
            $_SESSION['error'] = "Invalid code. Attempts left: ". (MAX_MFA_ATTEMPTS - $_SESSION['mfa_attempts']);
            header("Location: enter_code.php");
        }
    }
    exit();
}