<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// Ensure user is authenticated
Auth::requireAuth();

// Get active sessions
$sessions = Auth::getActiveSessions();
$user_id = Auth::id();

// Process actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Revoke specific session
        if (isset($_POST['revoke_session']) && !empty($_POST['token_id'])) {
            $token_id = (int)$_POST['token_id'];
            if (Auth::revokeSession($token_id)) {
                $message = 'Device has been successfully logged out.';
                $GLOBALS['authLogger']->info('Session revoked', [
                    'user_id' => $user_id,
                    'token_id' => $token_id,
                    'ip' => getClientIP()
                ]);
                // Refresh sessions list
                $sessions = Auth::getActiveSessions();
            } else {
                $error = 'Failed to revoke session. Please try again.';
            }
        }
        
        // Revoke all other sessions
        if (isset($_POST['revoke_all_other_sessions'])) {
            if (Auth::revokeOtherSessions()) {
                $message = 'All other devices have been successfully logged out.';
                $GLOBALS['authLogger']->info('All other sessions revoked', [
                    'user_id' => $user_id,
                    'ip' => getClientIP()
                ]);
                // Refresh sessions list
                $sessions = Auth::getActiveSessions();
            } else {
                $error = 'Failed to revoke other sessions. Please try again.';
            }
        }
    }
}

// Helper function to get device name from user agent
function getDeviceInfo($user_agent) {
    if (empty($user_agent)) {
        return 'Unknown device';
    }
    
    $device = 'Unknown device';
    
    // Detect browser
    if (preg_match('/MSIE|Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        if (preg_match('/Edge/i', $user_agent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/Edg/i', $user_agent)) {
            $browser = 'Microsoft Edge';
        } else {
            $browser = 'Chrome';
        }
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $browser = 'Opera';
    } else {
        $browser = 'Unknown browser';
    }
    
    // Detect OS
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
        $os = 'Mac OS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $user_agent)) {
        $os = 'iOS';
    } else {
        $os = 'Unknown OS';
    }
    
    // Detect if mobile
    $device_type = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $user_agent) ? 'Mobile' : 'Desktop';
    
    return "$browser on $os ($device_type)";
}

/**
 * Get the client's real IP address
 * 
 * @return string Client IP address
 */
function getClientIP() {
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

// Determine current session
$current_token = null;
$cookie_token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : null;
$current_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Login Sessions - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .sessions-table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f3f3f3;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
        }
        
        .sessions-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .sessions-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .current-session {
            background-color: #e8f4fd;
        }
        
        .current-session td:first-child {
            position: relative;
        }
        
        .current-session td:first-child::before {
            content: "Current";
            position: absolute;
            top: -8px;
            left: 15px;
            background-color: #0275d8;
            color: white;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .logout-all-form {
            margin-top: 20px;
        }
        
        .back-link {
            display: block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="container">
        <h1>Manage Your Login Sessions</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <p>Here you can see all devices where you're currently logged in. You can logout from any device you don't recognize or aren't using anymore.</p>
        
        <?php if (empty($sessions)): ?>
            <p>You don't have any active login sessions with the "Remember Me" option.</p>
        <?php else: ?>
            <table class="sessions-table">
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Location</th>
                        <th>Last Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): 
                        $is_current = ($cookie_token && isset($session['token']) && $cookie_token === $session['token']) ||
                                     (!$cookie_token && $current_user_agent === $session['user_agent']);
                    ?>
                        <tr class="<?php echo $is_current ? 'current-session' : ''; ?>">
                            <td><?php echo htmlspecialchars(getDeviceInfo($session['user_agent'])); ?></td>
                            <td><?php echo htmlspecialchars($session['ip_address'] ?? 'Unknown'); ?></td>
                            <td>
                                <?php 
                                    if (!empty($session['last_used_at'])) {
                                        $last_used = new DateTime($session['last_used_at']);
                                        echo $last_used->format('M j, Y g:i A');
                                    } else {
                                        echo 'Unknown';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if (!$is_current): ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="token_id" value="<?php echo (int)$session['token_id']; ?>">
                                        <button type="submit" name="revoke_session" class="btn btn-danger">Logout</button>
                                    </form>
                                <?php else: ?>
                                    <span class="btn btn-secondary" disabled>Current</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="logout-all-form">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" name="revoke_all_other_sessions" class="btn btn-danger">Logout from all other devices</button>
                </form>
            </div>
        <?php endif; ?>
        
        <a href="../HomePage/homePage.php" class="back-link">&larr; Back to Homepage</a>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html> 