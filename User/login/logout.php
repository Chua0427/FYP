<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// Record logout information for security auditing
$userId = $_SESSION['user_id'] ?? null;
$userEmail = $_SESSION['email'] ?? null;
$tokenPresent = isset($_COOKIE['auth_token']);
$tokenValue = $_COOKIE['auth_token'] ?? null;

// Get IP and user agent for logging
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// Log the logout request before performing the logout
if (isset($GLOBALS['authLogger']) && $userId) {
    $GLOBALS['authLogger']->info('User logout requested', [
        'user_id' => $userId,
        'email' => $userEmail,
        'ip' => $ip,
        'user_agent' => $userAgent,
        'had_token' => $tokenPresent
    ]);
}

// Check for "all_devices" parameter to logout from all devices
$allDevices = isset($_GET['all_devices']) && $_GET['all_devices'] === '1';

if ($allDevices && $userId) {
    // Revoke all tokens for this user
    if (isset($GLOBALS['authLogger'])) {
        $GLOBALS['authLogger']->notice('User logged out from all devices', [
            'user_id' => $userId,
            'email' => $userEmail,
            'ip' => $ip
        ]);
    }
    
    // Initialize TokenAuth to revoke all user tokens
    $tokenAuth = new TokenAuth();
    $tokenAuth->revokeAllUserTokens($userId);
}

// Perform regular logout which handles token revocation for current device
Auth::logout();

// For backward compatibility, also destroy the session
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

// Redirect to login page
header("Location: /FYP/FYP/User/login/login.php");
exit; 