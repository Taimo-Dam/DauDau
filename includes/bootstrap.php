<?php
// Only set session params and start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session configuration
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Start session
    session_start();
}

// Define core constants
define('ROOT_PATH', realpath(__DIR__ . '/../'));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('AVATAR_PATH', UPLOAD_PATH . '/avatars');
define('MUSIC_PATH', ROOT_PATH . '/music');