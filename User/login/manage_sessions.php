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
        return ['device' => 'Unknown device', 'icon' => 'fas fa-question-circle'];
    }
    
    $device = 'Unknown device';
    $icon = 'fas fa-question-circle';
    
    // Detect browser
    if (preg_match('/MSIE|Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
        $icon = 'fab fa-internet-explorer';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
        $icon = 'fab fa-firefox-browser';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        if (preg_match('/Edge/i', $user_agent)) {
            $browser = 'Microsoft Edge';
            $icon = 'fab fa-edge';
        } elseif (preg_match('/Edg/i', $user_agent)) {
            $browser = 'Microsoft Edge';
            $icon = 'fab fa-edge';
        } else {
            $browser = 'Chrome';
            $icon = 'fab fa-chrome';
        }
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
        $icon = 'fab fa-safari';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $browser = 'Opera';
        $icon = 'fab fa-opera';
    } else {
        $browser = 'Unknown browser';
        $icon = 'fas fa-globe';
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
        $icon = 'fab fa-android';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $user_agent)) {
        $os = 'iOS';
        $icon = 'fab fa-apple';
    } else {
        $os = 'Unknown OS';
    }
    
    // Detect if mobile
    $device_type = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $user_agent) ? 'Mobile' : 'Desktop';
    
    // Override icon for mobile devices
    if ($device_type === 'Mobile') {
        $icon = 'fas fa-mobile-alt';
    } elseif ($device_type === 'Desktop') {
        $icon = 'fas fa-desktop';
    }
    
    return [
        'device' => "$browser on $os ($device_type)",
        'icon' => $icon
    ];
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
        * {
            box-sizing: border-box;
        }
        
        .container {
            max-width: 1000px;
            margin: 0px auto 80px auto;
            padding: 0 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            top: 120px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.5;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .sessions-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .sessions-header {
            background: linear-gradient(135deg, orange 0%, orangered 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .sessions-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        .sessions-count {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .session-card {
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
            transition: background-color 0.2s ease;
        }
        
        .session-card:last-child {
            border-bottom: none;
        }
        
        .session-card:hover {
            background-color: #f8f9fa;
        }
        
        .current-session {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            position: relative;
        }
        
        .current-session::before {
            content: "Current Session";
            position: absolute;
            top: 10px;
            right: 20px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .session-info {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .session-icon {
            font-size: 24px;
            color: #6c757d;
            margin-top: 5px;
            min-width: 30px;
        }
        
        .session-details {
            flex: 1;
        }
        
        .device-name {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .session-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .session-actions {
            margin-top: 15px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            line-height: 1;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .btn-outline-danger {
            background-color: transparent;
            color: #dc3545;
            border: 2px solid #dc3545;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        
        .logout-all-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logout-all-section h3 {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .logout-all-section p {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .navigation {
            text-align: center;
            margin-top: 40px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .back-link:hover {
            background-color: #f8f9fa;
            color: #5a67d8;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .container {
                margin: 0px auto 60px auto;
                position: relative;
                top: 100px;
                padding: 0 15px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .session-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .session-icon {
                align-self: flex-start;
            }
            
            .session-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .current-session::before {
                position: static;
                display: inline-block;
                margin-bottom: 10px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 20px;
            }
            
            .session-card {
                padding: 15px;
            }
            
            .sessions-header {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-shield-alt" style="color: orangered; margin-right: 10px;"></i>
                Manage Device
            </h1>
            <p class="page-subtitle">
                Manage your active login sessions across all devices. Keep your account secure by logging out from devices you don't recognize or no longer use.
            </p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($sessions)): ?>
            <div class="sessions-container">
                <div class="empty-state">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>No Active Sessions Found</h3>
                    <p>You don't have any active login sessions with the "Remember Me" option enabled.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="sessions-container">
                <div class="sessions-header">
                    <h2><i class="fas fa-devices"></i> Active Login Sessions</h2>
                    <div class="sessions-count">
                        <?php echo count($sessions); ?> active session<?php echo count($sessions) > 1 ? 's' : ''; ?>
                    </div>
                </div>
                
                <?php foreach ($sessions as $session): 
                    $is_current = ($cookie_token && isset($session['token']) && $cookie_token === $session['token']) ||
                                 (!$cookie_token && $current_user_agent === $session['user_agent']);
                    $device_info = getDeviceInfo($session['user_agent']);
                ?>
                    <div class="session-card <?php echo $is_current ? 'current-session' : ''; ?>">
                        <div class="session-info">
                            <div class="session-icon">
                                <i class="<?php echo $device_info['icon']; ?>"></i>
                            </div>
                            <div class="session-details">
                                <div class="device-name"><?php echo htmlspecialchars($device_info['device']); ?></div>
                                <div class="session-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($session['ip_address'] ?? 'Unknown Location'); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>
                                            <?php 
                                                if (!empty($session['last_used_at'])) {
                                                    $last_used = new DateTime($session['last_used_at']);
                                                    echo 'Last active ' . $last_used->format('M j, Y \a\t g:i A');
                                                } else {
                                                    echo 'Last activity unknown';
                                                }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php if (!$is_current): ?>
                                    <div class="session-actions">
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="token_id" value="<?php echo (int)$session['token_id']; ?>">
                                            <button type="submit" name="revoke_session" class="btn btn-danger">
                                                <i class="fas fa-sign-out-alt"></i>
                                                Logout Device
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($sessions) > 1): ?>
                <div class="logout-all-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> Security Action</h3>
                    <p>If you notice any suspicious activity or want to secure your account, you can logout from all other devices with one click.</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <button type="submit" name="revoke_all_other_sessions" class="btn btn-outline-danger">
                            <i class="fas fa-power-off"></i>
                            Logout All Other Devices
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="navigation">
            <a href="../HomePage/homePage.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Homepage
            </a>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>