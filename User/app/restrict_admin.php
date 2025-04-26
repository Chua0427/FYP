<?php
declare(strict_types=1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Restrict admin users from accessing user pages.
 * This file should be included at the top of all user pages.
 */

// Check if logged in user is an admin (user_type = 2)
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 2) {
    // Log the attempt
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->warning('Admin attempted to access user page', [
            'user_id' => $_SESSION['user_id'] ?? 'unknown',
            'email' => $_SESSION['email'] ?? 'unknown',
            'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'page' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ]);
    }
    
    // Clear session data
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    // Redirect to admin login with error message
    header("Location: /FYP/FYP/Admin/login.php?error=" . urlencode('Admin accounts must use the admin panel'));
    exit;
}
?> 