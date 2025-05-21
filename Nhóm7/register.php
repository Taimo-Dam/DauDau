<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\dangky.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'includes/db.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $number = trim($_POST['number']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);

    // Validate form inputs
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Check if the email already exists
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $error = "An account with this email already exists.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $query = "INSERT INTO users (username, number, email, password) VALUES (:username, :number, :email, :password)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':number', $number);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->execute();

                $success = "Registration successful! Redirecting to login...";
                header("Refresh: 2; url=login.php"); // Redirect to login page after 2 seconds
                exit;
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again later.";
            error_log("Signup error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="stylesdangky+dangnhap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="info-box">
            <h1>Join Our Platform</h1>
            <p>You can be one of the <span class="highlight">members</span> of our platform by just adding some necessary information. If you already have an account on our website, you can just hit the <a href="login.php" class="login-link">Login button</a>.</p>
        </div>
        <div class="form-container">
            <div class="form-header">
                <a href="register.php" class="form-header-link active">Sign Up</a>
                <a href="login.php" class="form-header-link">Login</a>
            </div>

            <!-- Display error or success messages -->
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="register.php" method="post">
                <div class="input-container">
                <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter Your Name" required>
                </div>
                
                <div class="input-container">
                    <i class="fas fa-phone"></i>
                    <input type="text" id="number" name="number" placeholder="Enter Your Number" required>
                </div>
                
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter Your E-Mail" required>
                </div>
                
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
                </div>
                
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Your Password" required>
                </div>
                
                <button type="submit" class="form-button">Sign Up</button>
            </form>
            <div class="separator">Or</div>
            <button class="google-button">
                <img src="images/google.png" class="google-icon" width="20px" height="20px" />
                Sign Up With Google
            </button>
        </div>
    </div>
</body>
</html>