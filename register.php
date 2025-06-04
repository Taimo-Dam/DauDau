<?php
// Include bootstrap first - it will handle session
require_once 'includes/bootstrap.php';
require_once 'includes/init.php';
require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirectBasedOnUserRole();
    exit();
}

// Initialize variables
$error = '';
$success = '';
$formData = [
    'username' => '',
    'telephone' => '',
    'email' => '',
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'username' => trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)),
        'telephone' => trim(filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING)),
        'email' => trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)),
    ];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    try {
        // Validate form inputs
        if (empty($formData['username']) || empty($formData['email']) || empty($password)) {
            throw new Exception("All fields are required.");
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $formData['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email address is already registered.");
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $formData['username']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Username is already taken.");
        }

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (username, telephone, email, password, role, status) 
            VALUES (?, ?, ?, ?, 'user', 'active')
        ");
        $stmt->bind_param("ssss", 
            $formData['username'],
            $formData['telephone'],
            $formData['email'],
            $hashedPassword
        );

        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            logUserActivity($userId, 'register', 'New user registration');
            
            $success = "Registration successful! Redirecting to login...";
            header("Refresh: 2; url=login.php");
            exit();
        } else {
            throw new Exception("Registration failed. Please try again.");
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
    }
}

// Load template
$pageTitle = 'Register';
include 'templates/header.php';
?>

<link rel="stylesheet" href="assets/css/register.css">

<div class="container">
    <div class="info-box">
        <h1>Join Our Platform</h1>
        <p>Become a member of our music community.</p>
        <p> Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <div class="form-container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="register.php" method="post" id="register-form" novalidate>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?= htmlspecialchars($formData['username']) ?>"
                       required>
                <label for="username">Username</label>
            </div>

            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="tel" 
                       id="telephone" 
                       name="telephone" 
                       value="<?= htmlspecialchars($formData['telephone']) ?>"
                       pattern="[0-9]{10}"
                       required>
                <label for="telephone">Phone Number</label>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($formData['email']) ?>"
                       required>
                <label for="email">Email Address</label>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required>
                <label for="password">Password</label>
                <i class="fas fa-eye toggle-password" data-target="password"></i>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" 
                       id="confirm-password" 
                       name="confirm-password" 
                       required>
                <label for="confirm-password">Confirm Password</label>
                <i class="fas fa-eye toggle-password" data-target="confirm-password"></i>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <?php if (!empty($googleLoginUrl)): ?>
        <div class="social-login">
            <span>Or register with</span>
            <a href="<?= htmlspecialchars($googleLoginUrl) ?>" class="google-btn">
                <img src="assets/images/google.png" alt="Google">
                <span>Google</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/register.js"></script>
<?php include 'templates/footer.php'; ?>