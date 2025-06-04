<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\includes\config.php

/**
 * Website configuration settings
 */

// Site information
define('SITE_NAME', 'Tên Website');
define('SITE_URL', 'http://localhost/web/Nhóm7');
define('SITE_EMAIL', 'contact@yourwebsite.com');

// Security settings
define('CSRF_TOKEN_SECRET', 'your_csrf_secret_key_here');
// Session timeout defined below in Session and security section

// Google OAuth settings
// Define these constants if you want to enable Google login
// define('GOOGLE_CLIENT_ID', 'your_google_client_id_here');
// define('GOOGLE_CLIENT_SECRET', 'your_google_client_secret_here');
// define('GOOGLE_REDIRECT_URI', SITE_URL . '/google-callback.php');

// reCAPTCHA settings
// Define these constants if you want to enable reCAPTCHA
// define('RECAPTCHA_SITE_KEY', 'your_recaptcha_site_key_here');
// define('RECAPTCHA_SECRET_KEY', 'your_recaptcha_secret_key_here');

// Database tables
define('TABLE_USERS', 'users');
define('TABLE_USER_TOKENS', 'user_tokens');
define('TABLE_PASSWORD_RESETS', 'password_resets');
define('TABLE_LOGIN_ATTEMPTS', 'login_attempts');
define('TABLE_USER_ACTIVITY', 'user_activity_log');

// File upload settings
define('UPLOAD_DIR', 'uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Email settings
define('MAIL_FROM_NAME', SITE_NAME);
define('MAIL_FROM_EMAIL', SITE_EMAIL);

// Debug mode
define('DEBUG_MODE', getenv('APP_ENV') === 'production' ? false : true);

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Environment setting
define('ENVIRONMENT', 'development'); // 'development' or 'production'

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'MeandYou');

// Session and security
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes in seconds
define('REMEMBER_ME_EXPIRY', 2592000); // 30 days in seconds

// File upload limits
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/mp3', 'audio/wav']);

// Application URLs
define('BASE_URL', 'http://localhost/web/Nhom7');
define('ADMIN_URL', BASE_URL . '/admin');
define('ASSETS_URL', BASE_URL . '/assets');

// Google OAuth (if using)
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google-callback.php');

// Email settings (if using PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', '');
define('SMTP_NAME', 'MeandYou Music');

// Time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');