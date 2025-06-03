<?php
declare(strict_types=1);

// Only start session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/app/auth.php';

// Check if user is admin (types 2 or 3)
if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == '2' || $_SESSION['user_type'] == '3')) {
    // Set the admin view-only mode flag
    $_SESSION['admin_view_only'] = true;
    
    // Log the admin accessing user pages
    if (isset($GLOBALS['authLogger'])) {
        $GLOBALS['authLogger']->info('Admin accessed user area in view-only mode', [
            'admin_id' => $_SESSION['user_id'],
            'user_type' => $_SESSION['user_type'],
            'requested_url' => $_SERVER['REQUEST_URI'],
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
    
    // If this is a request that would change data or requires a redirect, show notification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
        isset($_GET['action']) || 
        strpos($_SERVER['REQUEST_URI'], 'checkout') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'payment') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'profile') !== false) {
        
        // Store the current URL for the redirect back
        $_SESSION['admin_redirect_from'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to admin notification page
        header('Location: /FYP/FYP/User/admin_notification.php');
        exit;
    }
}

// Regular user - continue normally
?> 