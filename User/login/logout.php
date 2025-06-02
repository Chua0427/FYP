<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// Log the logout event if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        $GLOBALS['authLogger']->info('User logout', [
            'user_id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    } catch (Exception $e) {
        // Log error but continue with logout
        $GLOBALS['logger']->error('Error logging logout', [
            'message' => $e->getMessage()
        ]);
    }
}

// Revoke token and clear cookie
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