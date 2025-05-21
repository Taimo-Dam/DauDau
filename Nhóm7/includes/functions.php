<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\includes\functions.php

/**
 * Functions for authentication and user management
 */
require_once 'db.php'; // Corrected path
if (!$conn) {
    die("Database connection is not initialized.");
}

// Check if login attempts are blocked
function isLoginBlocked($email) {
    global $conn; // Declare $conn as global
    
    try {
        $query = "SELECT attempts, last_attempt FROM login_attempts WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $attempts = $result['attempts'];
            $lastAttempt = strtotime($result['last_attempt']);
            $currentTime = time();
            
            if ($attempts >= 5 && ($currentTime - $lastAttempt) < 900) {
                return true;
            }
            
            if (($currentTime - $lastAttempt) >= 900) {
                resetLoginAttempts($email);
            }
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Login block check error: " . $e->getMessage());
        return false;
    }
}

// Increment login attempts
function incrementLoginAttempts($email) {
    global $conn; // Declare $conn as global

    try {
        $query = "INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("ss", $email, $ipAddress);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        error_log("Increment login attempts error: " . $e->getMessage());
        return false;
    }
}

// Reset login attempts
function resetLoginAttempts($email) {
    global $conn; // Declare $conn as global

    try {
        $query = "DELETE FROM login_attempts WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        error_log("Reset login attempts error: " . $e->getMessage());
        return false;
    }
}

// Update last login time
function updateLastLogin($userId) {
    global $conn; // Declare $conn as global

    try {
        $query = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        error_log("Update last login error: " . $e->getMessage());
        return false;
    }
}

// Log user activity
function logUserActivity($userId, $action, $description) {
    // Ensure we bring the connection variable into scope
    global $conn;
    
    $sql = "INSERT INTO user_activity_log (user_id, action, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return;
    }
    
    // Assuming user_id is integer and the others are strings
    $types = "iss";
    $stmt->bind_param($types, $userId, $action, $description);
    $stmt->execute();
}

// Generate password reset token
function generatePasswordResetToken($userId, $email) {
    global $pdo;
    
    try {
        // Generate a secure token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $query = "INSERT INTO password_resets (user_id, email, token, expires) 
                  VALUES (:user_id, :email, :token, :expires)
                  ON DUPLICATE KEY UPDATE token = :token, expires = :expires";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expiry);
        $stmt->execute();
        
        return $token;
    } catch (PDOException $e) {
        error_log("Password reset token error: " . $e->getMessage());
        return false;
    }
}

// Verify password reset token
function verifyPasswordResetToken($email, $token) {
    global $pdo;
    
    try {
        $query = "SELECT * FROM password_resets WHERE email = :email AND token = :token AND expires > NOW()";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Password reset verification error: " . $e->getMessage());
        return false;
    }
}

// Clear password reset token
function clearPasswordResetToken($email) {
    global $pdo;
    
    try {
        $query = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Password reset token clear error: " . $e->getMessage());
    }
}

// Send password reset email
function sendPasswordResetEmail($email, $token) {
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Build reset link
    $resetLink = SITE_URL . "/reset-password.php?email=" . urlencode($email) . "&token=" . $token;
    
    // Email subject
    $subject = "Password Reset Request";
    
    // Email message
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
    </head>
    <body>
        <h2>Password Reset Request</h2>
        <p>You recently requested to reset your password. Click the link below to reset it:</p>
        <p><a href='$resetLink'>Reset Your Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
        <p>Thank you,<br>The Website Team</p>
    </body>
    </html>
    ";
    
    // Set email headers
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . SITE_EMAIL,
        'Reply-To: ' . SITE_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    );
    
    // Send email
    return mail($email, $subject, $message, implode("\r\n", $headers));
}

// Check if user is logged in
function isLoggedIn() {
    error_log('isLoggedIn() called'); // Add this line
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    error_log('Session data inside isLoggedIn(): ' . print_r($_SESSION, true));
    if (isset($_SESSION['user_id'])) {
        error_log('User ID is set in session');
        return true;
    } else {
        error_log('User ID is NOT set in session');
        return false;
    }
}

// Check if user has specific role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['user_role'] === $role;
}
// Add this at the end of your functions.php file

function getRedirectUrlAfterLogin($role) {
    switch ($role) {
        case 'admin':
            return 'admin/dashboard.php';
        case 'user':
            return 'user/dashboard.php';
        default:
            return 'index.php';
    }
}
// Add this at the end of your existing functions

function redirectBasedOnUserRole() {
    if (!isset($_SESSION['user_role'])) {
        return;
    }
    
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'user':
            header('Location: user/dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}
// Require login for protected pages
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit;
    }
}

// Require specific role for access
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        header("Location: unauthorized.php");
        exit;
    }
}

// Get current user information
function getCurrentUser() {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get current user error: " . $e->getMessage());
        return null;
    }
}

// Clean session data on logout
function logoutUser() {
    // Remove remember me token if exists
    if (isset($_COOKIE['remember_me_email']) && isset($_COOKIE['remember_me_token'])) {
        global $pdo;
        
        try {
            $query = "DELETE FROM user_tokens WHERE email = :email AND token = :token";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $_COOKIE['remember_me_email']);
            $stmt->bindParam(':token', $_COOKIE['remember_me_token']);
            $stmt->execute();
            
            // Delete cookies
            setcookie('remember_me_email', '', time() - 3600, '/');
            setcookie('remember_me_token', '', time() - 3600, '/');
        } catch (PDOException $e) {
            error_log("Logout token deletion error: " . $e->getMessage());
        }
    }
    
    // Destroy session
    session_unset();
    session_destroy();
    
    // Start a new session
    session_start();
}

// Get user by email
function getUserByEmail($email) {
    global $pdo;
    
    try {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get user by email error: " . $e->getMessage());
        return null;
    }
}

// Update user password
function updateUserPassword($userId, $newPassword) {
    global $pdo;
    
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Update user password error: " . $e->getMessage());
        return false;
    }
}

function handleRememberMe($user) {
    global $conn;
    
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Delete any existing tokens for this user
    $query = "DELETE FROM user_tokens WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    
    // Insert new token
    $query = "INSERT INTO user_tokens (user_id, email, token, expires) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user['id'], $user['email'], $token, $expiry);
    $stmt->execute();
    
    // Set cookies
    setcookie('remember_me_email', $user['email'], time() + (86400 * 30), "/", "", true, true);
    setcookie('remember_me_token', $token, time() + (86400 * 30), "/", "", true, true);
}

function updatePasswordHash($userId, $password) {
    global $conn;
    
    if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $newHash, $userId);
        $stmt->execute();
    }
}