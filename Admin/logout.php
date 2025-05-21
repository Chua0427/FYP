<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';

// Log the logout event if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        // Create a PDO connection for logging
        $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Log the logout action
        $stmt = $pdo->prepare("INSERT INTO admin_activity_log 
                              (admin_id, activity_type, ip_address, details) 
                              VALUES (:admin_id, 'logout', :ip_address, 'Admin logout')");
        
        // Get the client IP using multiple header checks
        $ip_address = getClientIP();
        
        $stmt->bindParam(':admin_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        // Continue with logout even if logging fails
        error_log("Error logging admin logout: " . $e->getMessage());
    }
    
    // Revoke auth token if it exists
    if (isset($_COOKIE['auth_token'])) {
        try {
            $token = $_COOKIE['auth_token'];
            $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("UPDATE user_tokens SET is_revoked = 1 WHERE token = :token");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            
            // Clear auth token cookie
            setcookie(
                'auth_token',
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        } catch (Exception $e) {
            error_log("Error revoking admin token: " . $e->getMessage());
        }
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params["path"],
            'domain' => $params["domain"],
            'secure' => $params["secure"],
            'httponly' => $params["httponly"],
            'samesite' => 'Lax'
        ]
    );
}

// Destroy the session
session_destroy();

// Redirect to admin login page
header("Location: /FYP/FYP/Admin/login.php");
exit;

/**
 * Get the client's real IP address
 * 
 * @return string Client IP address
 */
function getClientIP(): string {
    // Check for proxy forwards
    $ip_keys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_CLIENT_IP',        // Shared internet
        'HTTP_X_FORWARDED_FOR',  // Common proxy
        'HTTP_X_FORWARDED',      // Common proxy
        'HTTP_X_CLUSTER_CLIENT_IP', // Load balancer
        'HTTP_FORWARDED_FOR',    // Common proxy
        'HTTP_FORWARDED',        // Common proxy
        'REMOTE_ADDR'            // Fallback
    ];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // If the IP is a comma-separated list, get the first one
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            
            // Validate IP format
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return 'Unknown';
}
?> 