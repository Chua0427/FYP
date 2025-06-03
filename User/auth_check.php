<?php
declare(strict_types=1);

// Only start session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/app/auth.php';

// Check if user is admin (types 2 or 3) and block access
if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == '2' || $_SESSION['user_type'] == '3')) {
    // Clear session and redirect to admin login
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 86400, '/');
    }
    session_destroy();
    
    // Remove auth token cookie
    setcookie('auth_token', '', time() - 86400, '/', '', false, true);
    
    // Log the unauthorized access attempt
    if (class_exists('Monolog\\Logger')) {
        $GLOBALS['authLogger']->warning('Admin attempted to access user area', [
            'user_type' => $_SESSION['user_type'],
            'requested_url' => $_SERVER['REQUEST_URI'],
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
    
    // Redirect with error message
    header('Location: /FYP/FYP/Admin/Dashboard/dashboard.php?error=unauthorized_access');
    exit;
}
// Regular user - continue normally
?> 