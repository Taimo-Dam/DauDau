<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug mode - remove in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include core files
require_once 'includes/bootstrap.php';
require_once 'includes/init.php';
require_once 'includes/auth_functions.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirectBasedOnUserRole();
    exit();
}

// Initialize variables
$error = '';
$success = '';
$email = '';

if (!isset($_SESSION['user_id'])) {
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    try {
        // Server-side validation
        validateLoginInput($email, $password);
        
        // Check login attempts
        if (isLoginBlocked($email)) {
            throw new Exception("Too many failed attempts. Please try again later or reset your password.");
        }

        // Attempt login
        $user = attemptLogin($email, $password);
        if ($user) {
            handleSuccessfulLogin($user, $remember);
            exit(); // Stop execution after redirect
        } else {
            handleFailedLogin($email);
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Login error for {$email}: {$e->getMessage()}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['success'] = "Welcome back!";
            
            // Redirect to index.php
            if (isset($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred during login";
        error_log("Login error: " . $e->getMessage());
    }
}

// Load template files
$pageTitle = 'Login';
include 'templates/header.php';
?>

<link rel="stylesheet" href="assets/css/login.css">

<div class="container">
    <div class="login-form">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" id="login-form" novalidate>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($email) ?>" 
                       required 
                       autocomplete="email">
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       autocomplete="current-password">
                <label for="password">Password</label>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? 
            <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<script src="assets/js/login.js"></script>
<?php include 'templates/footer.php'; ?>
