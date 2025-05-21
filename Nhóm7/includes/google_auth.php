<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\includes\google_auth.php

/**
 * Google OAuth authentication
 * Requires installation of Google Client Library:
 * composer require google/apiclient:^2.0
 */

// Check if Google API credentials are defined
if (!defined('GOOGLE_CLIENT_ID') || !defined('GOOGLE_CLIENT_SECRET') || !defined('GOOGLE_REDIRECT_URI')) {
    function getGoogleLoginUrl() {
        return '';
    }
    return;
}

// Include Composer autoloader (if you're using Composer)
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

// Get Google login URL
function getGoogleLoginUrl() {
    try {
        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);
        $client->addScope("email");
        $client->addScope("profile");
        
        return $client->createAuthUrl();
    } catch (Exception $e) {
        error_log("Google Auth Error: " . $e->getMessage());
        return '';
    }
}