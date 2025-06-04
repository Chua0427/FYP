<?php
declare(strict_types=1);

/**
 * Session Helper Functions
 * Provides safe ways to handle session management throughout the application
 */

// Check if the ensure_session_started function already exists (from init.php)
if (!function_exists('ensure_session_started')) {
    /**
     * Helper function to safely ensure a session is started.
     * Can be called multiple times safely.
     */
    function ensure_session_started(): void {
        if (!isset($GLOBALS['session_started']) && session_status() === PHP_SESSION_NONE) {
            // Configure session for better performance
            $isProduction = false;
            if (isset($_SERVER['SERVER_NAME'])) {
                $isProduction = !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
            }
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cache_limiter', $isProduction ? 'nocache' : 'private');
            ini_set('session.cookie_httponly', '1'); // Don't expose session cookie to JS
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? '1' : '0');
            
            // For PHP < 7.3, set the SameSite attribute via session.cookie_path
            if (PHP_VERSION_ID < 70300) {
                ini_set('session.cookie_path', '/; SameSite=Lax');
            }
            
            // Adjust session GC probability for better performance
            ini_set('session.gc_probability', '1');
            ini_set('session.gc_divisor', '100');
            
            // Try to start the session
            session_start();
            
            // Force session ID to refresh once per request to prevent session fixation
            if (!isset($_SESSION['__last_access'])) {
                session_regenerate_id(false);
                $_SESSION['__last_access'] = time();
            }
            
            $GLOBALS['session_started'] = true;
        }
    }
}

/**
 * Safely destroy the current session
 */
function end_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        $GLOBALS['session_started'] = false;
    }
}

/**
 * Set a session variable safely
 */
function set_session_var(string $key, $value): void {
    ensure_session_started();
    $_SESSION[$key] = $value;
}

/**
 * Get a session variable with optional default value
 */
function get_session_var(string $key, $default = null) {
    ensure_session_started();
    return $_SESSION[$key] ?? $default;
}

/**
 * Check if a session variable exists
 */
function has_session_var(string $key): bool {
    ensure_session_started();
    return isset($_SESSION[$key]);
}

/**
 * Remove a session variable
 */
function remove_session_var(string $key): void {
    ensure_session_started();
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Get all session variables
 */
function get_all_session_vars(): array {
    ensure_session_started();
    return $_SESSION;
}

/**
 * Session helper functions
 * Provides utilities for secure session management and token rotation
 */

class SessionHelper {
    /**
     * Regenerate session ID and rotate token if needed
     * 
     * @param bool $deleteOldSession Whether to delete old session
     * @param bool $rotateToken Whether to rotate the authentication token
     * @return bool Success status
     */
    public static function regenerateSession(bool $deleteOldSession = true, bool $rotateToken = false): bool {
        try {
            // Record old session ID and token for logging
            $oldSessionId = session_id();
            
            // Regenerate session ID
            if (!session_regenerate_id($deleteOldSession)) {
                error_log("Failed to regenerate session ID");
                return false;
            }
            
            $newSessionId = session_id();
            
            // Log session regeneration for security auditing
            if (isset($GLOBALS['authLogger'])) {
                $GLOBALS['authLogger']->info('Session regenerated', [
                    'user_id' => $_SESSION['user_id'] ?? 'guest',
                    'old_session_id' => substr($oldSessionId, 0, 8) . '...',
                    'new_session_id' => substr($newSessionId, 0, 8) . '...',
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
            
            // Rotate token if requested
            if ($rotateToken && isset($_COOKIE['auth_token'])) {
                // Require auth_token.php and auth.php only when needed
                require_once __DIR__ . '/auth.php';
                require_once __DIR__ . '/token.php';
                
                $oldToken = $_COOKIE['auth_token'];
                $tokenAuth = new TokenAuth();
                $user = $tokenAuth->validateToken($oldToken);
                
                if ($user) {
                    // Create a new token for the same user
                    $userId = (int) $user['user_id'];
                    $remember = false; // Default to short-lived token
                    
                    // Determine if the old token was long-lived
                    $tokenParts = explode('.', $oldToken);
                    $tokenId = $tokenParts[0] ?? '';
                    
                    if ($tokenId) {
                        try {
                            $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
                            $stmt = $pdo->prepare("SELECT expires_at FROM user_tokens WHERE token_id = :token_id AND user_id = :user_id");
                            $stmt->bindParam(':token_id', $tokenId, PDO::PARAM_STR);
                            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($result) {
                                // Check if token was long-lived
                                $expiryDate = strtotime($result['expires_at']);
                                $remember = ($expiryDate - time() > 86400 * 7); // Consider it long-lived if more than a week
                            }
                        } catch (Exception $e) {
                            error_log("Failed to check token expiry: " . $e->getMessage());
                        }
                    }
                    
                    // Generate new token with same expiry type
                    $newToken = Auth::login($userId, $user, $remember);
                    
                    // Revoke the old token
                    $tokenAuth->revokeToken($oldToken);
                    
                    // Log token rotation
                    if (isset($GLOBALS['authLogger'])) {
                        $GLOBALS['authLogger']->info('Authentication token rotated during session regeneration', [
                            'user_id' => $userId,
                            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]);
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Session regeneration error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set secure session cookie parameters
     * Should be called before session_start()
     */
    public static function setSecureSessionParams(): void {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $httpOnly = true;
        $sameSite = 'Lax'; // Less restrictive than 'Strict' to allow redirects from external sites
        
        // PHP 7.3+ supports SameSite in session_set_cookie_params
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,             // 0 = Until browser is closed
                'path' => '/',
                'domain' => '',              // Current domain only
                'secure' => $secure,         // Only send over HTTPS if available
                'httponly' => $httpOnly,     // Not accessible via JavaScript
                'samesite' => $sameSite      // Cross-site request protection
            ]);
        } else {
            // Older PHP versions
            session_set_cookie_params(
                0,                          // lifetime
                '/; SameSite=' . $sameSite, // path with SameSite parameter
                '',                         // domain
                $secure,                    // secure
                $httpOnly                   // httponly
            );
        }
    }
    
    /**
     * Add security headers to protect against XSS, clickjacking, etc.
     * Call this at the beginning of each page
     */
    public static function addSecurityHeaders(): void {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Help prevent XSS attacks
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enforce HTTPS
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        if ($secure) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Allow only same origin by default, customize for specific needs
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://js.stripe.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:; connect-src 'self' https://api.stripe.com; frame-src https://js.stripe.com; frame-ancestors 'none'");
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions policy
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)');
    }
    
    /**
     * Get a cross-site request forgery token.
     * Generates a new token if none exists or if forced.
     *
     * @param bool $forceNew Force creation of a new token
     * @return string CSRF token
     */
    public static function getCsrfToken(bool $forceNew = false): string {
        if ($forceNew || !isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify a CSRF token
     *
     * @param string $token Token to verify
     * @return bool Whether token is valid
     */
    public static function verifyCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Debug session state - useful for troubleshooting session issues
 * @param string $label A label to identify this debug point
 * @param string $logfile Optional file path to write log to
 * @return array Session debug info
 */
function debug_session(string $label = 'Session Debug', ?string $logfile = null): array {
    $debug = [
        'label' => $label,
        'timestamp' => date('Y-m-d H:i:s'),
        'session_id' => session_id(),
        'session_status' => session_status(),
        'session_name' => session_name(),
        'session_started' => isset($GLOBALS['session_started']),
        'session_vars' => []
    ];
    
    // Include sanitized session variables
    if (isset($_SESSION) && is_array($_SESSION)) {
        foreach ($_SESSION as $key => $value) {
            if (is_scalar($value)) {
                $debug['session_vars'][$key] = $value;
            } else {
                $debug['session_vars'][$key] = '('. gettype($value) . ')';
            }
        }
    }
    
    // Add request info
    $debug['request'] = [
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'referer' => $_SERVER['HTTP_REFERER'] ?? ''
    ];
    
    // Write to log if requested
    if ($logfile) {
        $logdir = dirname($logfile);
        if (!file_exists($logdir)) {
            mkdir($logdir, 0777, true);
        }
        
        $logEntry = date('[Y-m-d H:i:s]') . " $label: " . json_encode($debug) . PHP_EOL;
        file_put_contents($logfile, $logEntry, FILE_APPEND);
    }
    
    return $debug;
} 