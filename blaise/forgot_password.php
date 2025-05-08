<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'] ?? '';
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: forgot_password.php");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Email not registered";
        header("Location: forgot_password.php");
        exit();
    }

    if ($method == 'email') {
        // Generate and store OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', time() + 600); // 10 minutes
        
        $update = $conn->prepare("UPDATE users SET 
            mfa_code = ?,
            mfa_expiry = ?,
            login_attempts = 0 
            WHERE email = ?");
        $update->bind_param("sss", $otp, $expiry, $email);
        $update->execute();

        // Simulate email sending
        // In production, use PHPMailer or similar
        error_log("OTP for $email: $otp"); 

        $_SESSION['reset_email'] = $email;
        $_SESSION['otp_attempts'] = 0;
        header("Location: verify_code.php");
        exit();

    } elseif ($method == 'security') {
        $_SESSION['reset_email'] = $email;
        $_SESSION['security_attempts'] = 0;
        header("Location: security_question.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <style>
        .container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .hidden { display: none; }
        .error { color: red; margin-bottom: 1rem; }
        .method-toggle {
            margin-top: 1rem;
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <h2>Password Recovery</h2>
        
        <div id="email-method">
            <form method="POST">
                <div>
                    <label>Email Address:</label>
                    <input type="email" name="email" required>
                </div>
                <input type="hidden" name="method" value="email">
                <button type="submit">Send Verification Code</button>
            </form>
        </div>

        <div id="security-method" class="hidden">
            <form method="POST">
                <div>
                    <label>Email Address:</label>
                    <input type="email" name="email" required>
                </div>
                <input type="hidden" name="method" value="security">
                <button type="submit">Answer Security Question</button>
            </form>
        </div>

        <div class="method-toggle" onclick="toggleMethod()">
            Try <?= strpos($_SERVER['HTTP_REFERER'] ?? '', 'security') ? 'email' : 'security question' ?> method
        </div>

        <p>Remember your password? <a href="login.html">Login here</a></p>
    </div>

    <script>
        function toggleMethod() {
            document.getElementById('email-method').classList.toggle('hidden');
            document.getElementById('security-method').classList.toggle('hidden');
        }
    </script>
</body>
</html>