<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\login.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files for database connection, configuration, and helper functions
require_once 'includes/db.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

/*
// Uncomment and update if using Google login
// define('RECAPTCHA_SITE_KEY', 'your_recaptcha_site_key_here');
*/

if (isset($_SESSION['user_id'])) {
    // Redirect if already logged in
    redirectBasedOnUserRole();
}

// Initialize variables
$error = '';
$success = '';
$email = '';

// Check for "remember me" cookies
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me_email']) && isset($_COOKIE['remember_me_token'])) {
    $email = $_COOKIE['remember_me_email'];
    $token = $_COOKIE['remember_me_token'];
    
    try {
        // Verify remember me token
        $query = "SELECT * FROM user_tokens WHERE email = ? AND token = ? AND expires > NOW()";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // If token is valid, get user data
            $query = "SELECT * FROM users WHERE email = ? AND status = 'active'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                updateLastLogin($user['id']);
                redirectBasedOnUserRole();
            }
        }
    } catch (Exception $e) {
        error_log("Remember me token verification error: " . $e->getMessage());
    }
}

// Initialize Google login if configured
$googleLoginUrl = '';
if (defined('GOOGLE_CLIENT_ID') && defined('GOOGLE_CLIENT_SECRET')) {
    require_once 'includes/google_auth.php';
    $googleLoginUrl = getGoogleLoginUrl();
}

// Handle form submission on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            if (isLoginBlocked($email)) {
                $error = "Too many failed login attempts. Please try again later or reset your password.";
            } else {
                // Check if user exists and is active
                $query = "SELECT * FROM users WHERE email = ? AND status = 'active'";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                // For plain text password comparison (update to password_verify() if passwords are hashed)
                //if ($user && $user['password'] === $password) {
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Reset login attempts and update login time
                    resetLoginAttempts($email);
                    updateLastLogin($user['id']);
                    
                    // Handle "Remember Me" feature
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                        
                        $query = "INSERT INTO user_tokens (user_id, email, token, expires) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE token = ?, expires = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("isssss", $user['id'], $email, $token, $expiry, $token, $expiry);
                        $stmt->execute();
                        
                        setcookie('remember_me_email', $email, time() + (86400 * 30), "/", "", true, true);
                        setcookie('remember_me_token', $token, time() + (86400 * 30), "/", "", true, true);
                    }
                    
                    $success = "Login successful! Redirecting...";
                    logUserActivity($user['id'], 'login', 'User logged in successfully');
                    header("Refresh: 2; url=" . getRedirectUrlAfterLogin($user['role']));
                    exit;
                } else {
                    $error = "Invalid email or password.";
                    incrementLoginAttempts($email);
                }
            }
        } catch (Exception $e) {
            $error = "An error occurred. Please try again later.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="stylesdangky+dangnhap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <div class="info-box">
            <h1>Join Our Platform</h1>
            <p>You can be one of the <span class="highlight">members</span> of our platform by just adding some necessary information. If you don't have an account yet, you can <a href="dangky.php" class="signup-link">Sign Up here</a>.</p>
        </div>
        <div class="form-container">
            <div class="form-header">
                <a href="register.php" class="form-header-link">Sign Up</a>
                <a href="login.php" class="form-header-link active">Login</a>
            </div>
            
            <!-- Display error or success messages -->
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="post" id="login-form">
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter Your Email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
                <div class="form-options">
                    <div class="checkbox-container">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="form-button">Login</button>
            </form>
            
            <div class="separator">Or</div>
            
            <?php if (!empty($googleLoginUrl)): ?>
            <a href="<?php echo htmlspecialchars($googleLoginUrl); ?>" class="google-button">
                <img src="images/google.png" class="google-icon" width="20px" height="20px"/>
                Login With Google
            </a>
            <?php else: ?>
            <button class="google-button" onclick="alert('Google login is not configured');">
                <img src="images/google.png" class="google-icon" width="20px" height="20px"/>
                Login With Google
            </button>
            <?php endif; ?>
            
            <div class="back-to-home">
                <a href="index.php"><i class="fas fa-home"></i> Back to Home</a>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Simple form validation
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(event) {
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                if (!email || !password) {
                    alert('Please fill in both email and password.');
                    event.preventDefault();
                }
            });
        }
    });
    </script>
</body>
</html>
<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';
?>

