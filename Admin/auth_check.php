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

// Check for and delete any user authentication tokens when an admin logs in
function clearUserTokens(): void {
    if (isset($_COOKIE['auth_token'])) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Parse the token to get the identifier part
            $token = $_COOKIE['auth_token'];
            $parts = explode('.', $token);
            if (count($parts) === 2) {
                $token_id = $parts[0];
                
                // Revoke the token in the database
                $stmt = $pdo->prepare("UPDATE user_tokens SET is_revoked = 1 WHERE token = :token_hash");
                $token_hash = hash('sha256', $parts[1]); // Hash the secret part
                $stmt->bindParam(':token_hash', $token_hash);
                $stmt->execute();
                
                // Delete the cookie
                setcookie('auth_token', '', time() - 3600, '/');
                
                // Log the action
                if (isset($GLOBALS['authLogger'])) {
                    $GLOBALS['authLogger']->info('User auth token deleted due to admin login', [
                        'admin_user_id' => $_SESSION['user_id'],
                        'token_id' => $token_id,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("Failed to clear user token: " . $e->getMessage());
        }
    }
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
} else {
    // Admin is logged in, check and clear any user tokens
    clearUserTokens();
    
    // Set a flag to indicate an admin is logged in
    $_SESSION['admin_logged_in'] = true;
}
?> 