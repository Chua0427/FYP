<?php
declare(strict_types=1);

/**
 * Authentication check helper to be included in pages that require login
 */

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions
require_once __DIR__ . '/auth.php';

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is authenticated
$is_authenticated = Auth::check();

// For pages requiring authentication, redirect to login
if (!$is_authenticated) {
    // Store the current URL for redirection after login
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
                  "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    // Redirect to login page with return URL
    header("Location: /FYP/User/login/login.php?redirect=" . urlencode($current_url));
    exit;
}

// Get user data
$user_data = Auth::user();

// Update session for backward compatibility if needed
if (!isset($_SESSION['user_id']) && isset($user_data['user_id'])) {
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['first_name'] = $user_data['first_name'] ?? '';
    $_SESSION['last_name'] = $user_data['last_name'] ?? '';
    $_SESSION['email'] = $user_data['email'] ?? '';
    $_SESSION['user_type'] = $user_data['user_type'] ?? 0;
} 