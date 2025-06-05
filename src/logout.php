<?php
// Start or resume the session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();
}

// Redirect to login page
header("Location: login.php");
exit;
?>
