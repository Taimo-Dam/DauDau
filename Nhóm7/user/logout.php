<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions using correct paths
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Log the logout activity if user was logged in
if (isset($_SESSION['user_id'])) {
    try {
        // Log user activity
        $userId = $_SESSION['user_id'];
        logUserActivity($userId, 'logout', 'User logged out successfully');
        
        // Remove remember me cookies if they exist
        if (isset($_COOKIE['remember_me_email']) && isset($_COOKIE['remember_me_token'])) {
            // Delete token from database using mysqli instead of PDO
            $email = $_COOKIE['remember_me_email'];
            $stmt = $conn->prepare("DELETE FROM user_tokens WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            // Remove cookies
            setcookie('remember_me_email', '', time() - 3600, '/');
            setcookie('remember_me_token', '', time() - 3600, '/');
        }
    } catch (Exception $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Destroy all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

header("Location: ../login.php");
exit();
