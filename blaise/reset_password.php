<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['reset_verified'])) {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: forgot_password.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Password reset successful!";
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong!";
        header("Location: reset_password.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form method="POST">
        <div class="form-group">
            <input type="password" name="new_password" placeholder="Enter new password" required>
        </div>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
