<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    // Validate password length
    if (strlen($new_password) < 8) {
        $message = "New password must be at least 8 characters long.";
    }
    // Check if passwords match
    elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    }
    else {
        // Get current password hash from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($current_password, $user['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password in database
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $message = "Password updated successfully!";
            } else {
                $message = "Error updating password.";
            }
            $update_stmt->close();
        } else {
            $message = "Current password is incorrect.";
        }
        $stmt->close();
    }
}

// Redirect back to settings page with message
header("Location: setting.php?message=" . urlencode($message));
exit();
?> 