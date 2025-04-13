<?php
/**
 * Authentication check file
 * Include this at the top of protected pages
 */

require_once __DIR__ . '/auth.php';

// Start session for backward compatibility
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if authenticated with token
if (Auth::check()) {
    $user = Auth::user();
    
    // Set session variables for backward compatibility
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
    }
} 
// Not authenticated with token, but session exists (legacy)
elseif (isset($_SESSION['user_id'])) {
    // Get user from database to verify session is valid
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate token for this user
            Auth::login($user['user_id'], $user);
        } else {
            // Invalid session, clear it
            $_SESSION = array();
            session_destroy();
            
            // Redirect to login
            header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    } catch (Exception $e) {
        // Error getting user data, log error
        error_log("Session validation error: " . $e->getMessage());
    }
} 
// Not authenticated at all
else {
    // Redirect to login
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?> 