<?php
require_once 'config.php';
require_once 'db.php';

/**
 * Attempts to log in a user with the given credentials
 * @param string $email User's email
 * @param string $password User's password
 * @return array|false User data if successful, false otherwise
 */
function attemptLogin($email, $password) {
    global $conn;
    
    try {
        // Validate input
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        $stmt = $conn->prepare("
            SELECT id, email, password, role, status, login_attempts, last_attempt 
            FROM users 
            WHERE email = ? AND status = 'active'
        ");
        
        if (!$stmt) {
            error_log("Database prepare error: " . $conn->error);
            throw new Exception("Database error occurred");
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            error_log("Login attempt failed: User not found - " . $email);
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            // Log successful login
            error_log("Successful login: " . $email);
            return $user;
        }
        
        error_log("Failed login attempt: Invalid password for " . $email);
        return false;
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handles successful login operations
 * @param array $user User data
 * @param bool $remember Whether to set remember me token
 */
function handleSuccessfulLogin($user, $remember = false) {
    try {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        if ($remember) {
            setRememberMeToken($user);
        }
        
        // Update user's last login time and reset attempts
        updateLastLogin($user['id']);
        resetLoginAttempts($user['email']);
        
        // Log the successful login using the existing function
        logUserActivity($user['id'], 'login', 'Successful login');
        
        redirectBasedOnUserRole();
        
    } catch (Exception $e) {
        error_log("Error in handleSuccessfulLogin: " . $e->getMessage());
        throw new Exception("Error processing login. Please try again.");
    }
}

function getUserById($userId) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
/**
 * Handles failed login attempts
 * @param string $email User's email
 */
function handleFailedLogin($email) {
    try {
        $attempts = incrementLoginAttempts($email);
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            logUserActivity(null, 'login_blocked', "Account blocked due to multiple failed attempts: $email");
            throw new Exception("Too many failed attempts. Account temporarily blocked.");
        }
        
        $remainingAttempts = MAX_LOGIN_ATTEMPTS - $attempts;
        throw new Exception("Invalid credentials. $remainingAttempts attempts remaining.");
        
    } catch (Exception $e) {
        error_log("Failed login handling error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Sets remember me token for user
 * @param array $user User data
 */
function setRememberMeToken($user) {
    global $conn;
    
    try {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $conn->prepare("
            INSERT INTO user_tokens (user_id, token, expires) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->bind_param("iss", $user['id'], $token, $expiry);
        $stmt->execute();
        
        setcookie(
            'remember_me_token',
            $token,
            strtotime('+30 days'),
            '/',
            '',
            true,    // Secure flag
            true     // HTTPOnly flag
        );
        
    } catch (Exception $e) {
        error_log("Remember me token error: " . $e->getMessage());
        // Don't throw - non-critical feature
    }
}

function validateLoginInput($email, $password) {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address.");
    }
    if (empty($password)) {
        throw new Exception("Please enter your password.");
    }
}


function getUserFromRememberToken($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Validates user credentials for login
 * @param string $email User's email
 * @param string $password User's password
 * @return array|false User data if successful, false otherwise
 */
function validateLoginCredentials($email, $password) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Login validation error: " . $e->getMessage());
        return false;
    }
}
