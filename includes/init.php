<?php
// Load bootstrap first
require_once __DIR__ . '/bootstrap.php';

// Load configuration
require_once __DIR__ . '/config.php';

// Load database connection
require_once __DIR__ . '/db.php';

// Load core functions first
require_once __DIR__ . '/functions.php';

// Load authentication functions last
require_once __DIR__ . '/auth_functions.php';

// Set error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle remember me functionality
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me_token'])) {
    $user = getUserFromRememberToken($_COOKIE['remember_me_token']);
    if ($user) {
        handleRememberMe($user);
    }
}
if (isset($_SESSION['user_id'])) {
    ($_SESSION['user_id']);
} else {
    $currentUser = null;
}
// Create required directories
$directories = [UPLOAD_PATH, AVATAR_PATH, MUSIC_PATH];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Utility functions
function clean($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function redirectTo($location) {
    header("Location: $location");
    exit();
}