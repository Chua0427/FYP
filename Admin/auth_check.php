<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Admin Authentication Check
 * This file should be included at the top of all admin pages
 * to prevent unauthorized access
 */

// Function to verify admin session is valid
function isValidAdminSession(): bool {
    // Check if user is logged in and has admin role
    if (!isset($_SESSION['user_id']) || 
        !isset($_SESSION['user_type']) || 
        ($_SESSION['user_type'] != '2' && $_SESSION['user_type'] != '3')) {
        return false;
    }
    
    // Check if authentication fingerprint matches
    $expectedFingerprint = hash('sha256', 
        $_SERVER['HTTP_USER_AGENT'] . 
        ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
        $_SESSION['user_id']
    );
    
    if (!isset($_SESSION['auth_fingerprint']) || $_SESSION['auth_fingerprint'] !== $expectedFingerprint) {
        return false;
    }
    
    return true;
}

// If not properly authenticated, redirect to login
if (!isValidAdminSession()) {
    // Clear any potentially invalid session data
    $_SESSION = array();
    session_destroy();
    
    // Start a new session to store potential error message
    session_start();
    
    // Set unauthorized access error
    $_SESSION['login_error'] = 'Please log in to access admin area';
    
    // Redirect to login page
    header('Location: /FYP/FYP/Admin/login.php');
    exit;
}
?> 