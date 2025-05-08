<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verification_code'];

    if ($verification_code == $_SESSION['verification_code'] && time() < $_SESSION['code_expires']) {
        $_SESSION['reset_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired verification code!";
        header("Location: verify_code.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
</head>
<body>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="verification_code" placeholder="Enter verification code" required>
        </div>
        <button type="submit">Verify</button>
    </form>
</body>
</html>

