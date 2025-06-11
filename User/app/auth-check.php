<?php
declare(strict_types=1);

/**
 * Authentication check with popup notification
 * Include this at the top of protected pages
 */

// Start session for backward compatibility
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
$is_authenticated = isset($_SESSION['user_id']);

// Add auth-notification resources
function add_auth_notification_resources() {
    echo '<link rel="stylesheet" href="/FYP/User/app/services/auth-notification.css">';
    echo '<script defer src="/FYP/User/app/services/auth-notification.js"></script>';
}

// Mark an element as requiring authentication
function requires_auth_attr($echo = true) {
    $attr = 'data-requires-auth="true"';
    if ($echo) {
        echo $attr;
    }
    return $attr;
}

// Decide if user needs to be redirected based on auth status
function check_auth_redirect($redirect = true) {
    if (!isset($_SESSION['user_id']) && $redirect) {
        header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    return isset($_SESSION['user_id']);
}
?> 