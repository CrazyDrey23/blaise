<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<h2>Welcome, <?php echo $_SESSION['fullname']; ?>!</h2>
<a href="Task2.html">Logout</a>
