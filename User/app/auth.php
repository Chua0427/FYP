<?php
declare(strict_types=1);

/**
 * Authentication middleware
 */

require_once __DIR__ . '/token.php';

class Auth {
    private static $tokenAuth;
    private static $user = null;
    
    /**
     * Initialize TokenAuth instance
     */
    public static function init(): void {
        if (!self::$tokenAuth) {
            self::$tokenAuth = new TokenAuth();
        }
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool Authentication status
     */
    public static function check(): bool {
        self::init();
        
        // Immediately logout if a token cookie exists but is invalid or revoked
        $cookieToken = self::$tokenAuth->parseToken();
        if ($cookieToken !== null) {
            $valid = self::$tokenAuth->validateToken($cookieToken);
            if (!$valid) {
                // Token revoked or expired: logout immediately
                self::logout();
                return false;
            }
        }
        
        // If user is already loaded, return true
        if (self::$user) {
            return true;
        }
        
        // Check session authentication first (for non-Remember Me logins)
        if (isset($_SESSION['user_id']) && isset($_SESSION['auth_fingerprint'])) {
            // Verify the fingerprint to prevent session hijacking
            $expected_fingerprint = hash('sha256', 
                $_SERVER['HTTP_USER_AGENT'] . 
                ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
                $_SESSION['user_id']
            );
            
            if ($_SESSION['auth_fingerprint'] === $expected_fingerprint) {
                // Load user data from session
                try {
                    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        self::$user = $user;
                        
                        // Log periodic session activity for security auditing
                        $current_time = time();
                        if (!isset($_SESSION['last_activity_log']) || ($current_time - $_SESSION['last_activity_log'] > 3600)) {
                            $_SESSION['last_activity_log'] = $current_time;
                            if (isset($GLOBALS['authLogger'])) {
                                $GLOBALS['authLogger']->info('Session activity', [
                                    'user_id' => $user['user_id'],
                                    'email' => $user['email'],
                                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                                ]);
                            }
                        }
                        
                        return true;
                    } else {
                        // User not found in database but exists in session
                        // Clear invalid session data
                        unset($_SESSION['user_id']);
                        unset($_SESSION['auth_fingerprint']);
                    }
                } catch (Exception $e) {
                    error_log("Session auth error: " . $e->getMessage());
                }
            } else {
                // Fingerprint mismatch - potential session hijacking attempt
                error_log("Auth fingerprint mismatch for user ID " . $_SESSION['user_id']);
                
                // Log the security event
                if (isset($GLOBALS['authLogger'])) {
                    $GLOBALS['authLogger']->warning('Session fingerprint mismatch - potential hijacking attempt', [
                        'user_id' => $_SESSION['user_id'],
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                    ]);
                }
                
                // Clear potentially compromised session
                unset($_SESSION['user_id']);
                unset($_SESSION['auth_fingerprint']);
            }
        }
        
        // If no valid session auth, try token auth
        $token = self::$tokenAuth->parseToken();
        if (!$token) {
            return false;
        }
        
        // Validate token
        $user = self::$tokenAuth->validateToken($token);
        if (!$user) {
            return false;
        }
        
        // Store user data
        self::$user = $user;
        
        // Automatic session upgrade on first token use
        // If we're here, it means we have a valid token but no valid session
        // Create a new session to allow seamless navigation on the site
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'] ?? '';
        $_SESSION['last_name'] = $user['last_name'] ?? '';
        $_SESSION['email'] = $user['email'] ?? '';
        $_SESSION['user_type'] = $user['user_type'] ?? '';
        
        // Set session fingerprint
        $_SESSION['auth_fingerprint'] = hash('sha256', 
            $_SERVER['HTTP_USER_AGENT'] . 
            ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
            $user['user_id']
        );
        
        // Log the automatic session upgrade for security purposes
        if (isset($GLOBALS['authLogger'])) {
            $GLOBALS['authLogger']->info('Automatic session upgrade from token', [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        }
        
        return true;
    }
    
    /**
     * Get current authenticated user
     * 
     * @return array|null User data if authenticated, null otherwise
     */
    public static function user(): ?array {
        if (self::check()) {
            return self::$user;
        }
        return null;
    }
    
    /**
     * Get user ID of authenticated user
     * 
     * @return int|null User ID if authenticated, null otherwise
     */
    public static function id(): ?int {
        if (self::check()) {
            return (int)self::$user['user_id'];
        }
        return null;
    }
    
    /**
     * Require authentication to access a page
     * Redirects to login if not authenticated
     * 
     * @param string|null $redirect_url Custom redirect URL
     * @return void
     */
    public static function requireAuth(?string $redirect_url = null): void {
        if (!self::check()) {
            $current_url = urlencode($_SERVER['REQUEST_URI']);
            $redirect = $redirect_url ?? "/FYP/FYP/User/login/login.php?redirect={$current_url}";
            header("Location: {$redirect}");
            exit;
        }
    }
    
    /**
     * Login a user with session only (no persistent token)
     * 
     * @param array $user_data User data to store
     * @return void
     */
    public static function loginWithoutToken(array $user_data): void {
        // Store user data in memory
        self::$user = $user_data;
        
        // No cookie is set, authentication relies only on session
    }
    
    /**
     * Login a user and generate a token
     * 
     * @param int $user_id User ID
     * @param array $user_data User data to store
     * @param bool $remember Whether to set a long-lived cookie
     * @return string Generated token
     */
    public static function login(int $user_id, array $user_data, bool $remember = false): string {
        self::init();
        
        // Generate token with appropriate expiry: infinite if remember, 1 day otherwise
        if ($remember) {
            $token = self::$tokenAuth->generateToken($user_id, -1);
            // Cookie expiry far in the future (10 years)
            $cookieExpiry = time() + (86400 * 365 * 10);
        } else {
            $token = self::$tokenAuth->generateToken($user_id, 86400);
            // Cookie expiry for 1 day
            $cookieExpiry = time() + 86400;
        }
        
        // Store user data
        self::$user = $user_data;
        
        // Set cookie with HttpOnly and secure flags
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie(
            'auth_token',
            $token,
            [
                'expires' => $cookieExpiry,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
        
        // Also set the token in session for API routes that need it
        $_SESSION['auth_token'] = $token;
        
        // Log successful login with token
        if (isset($GLOBALS['authLogger'])) {
            $token_parts = explode('.', $token);
            $token_id = $token_parts[0] ?? 'unknown';
            
            $GLOBALS['authLogger']->info('User logged in with token', [
                'user_id' => $user_id,
                'email' => $user_data['email'] ?? 'unknown',
                'token_id' => $token_id, // Only log the public part
                'remember_me' => $remember,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        }
        
        return $token;
    }
    
    /**
     * Get all active sessions/devices for the current user
     * @return array List of active tokens with device information
     */
    public static function getActiveSessions(): array {
        self::init();
        
        $user_id = self::id();
        if (!$user_id) {
            return [];
        }
        
        return self::$tokenAuth->getUserTokens($user_id);
    }
    
    /**
     * Revoke a specific session/device
     * @param int $token_id Token ID to revoke
     * @return bool Success status
     */
    public static function revokeSession(int $token_id): bool {
        self::init();
        
        $user_id = self::id();
        if (!$user_id) {
            return false;
        }
        
        return self::$tokenAuth->revokeTokenById($token_id, $user_id);
    }
    
    /**
     * Revoke all sessions except the current one
     * @return bool Success status
     */
    public static function revokeOtherSessions(): bool {
        self::init();
        
        $user_id = self::id();
        $token = self::$tokenAuth->parseToken();
        
        if (!$user_id || !$token) {
            return false;
        }
        
        return self::$tokenAuth->revokeOtherUserTokens($user_id, $token);
    }
    
    /**
     * Logout current user
     * @return void
     */
    public static function logout(): void {
        self::init();
        
        // Get token
        $token = self::$tokenAuth->parseToken();
        
        // Log the logout event before revoking the token
        if (self::$user && isset($GLOBALS['authLogger'])) {
            $GLOBALS['authLogger']->info('User logged out', [
                'user_id' => self::$user['user_id'],
                'email' => self::$user['email'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
        
        // Revoke token if exists
        if ($token) {
            // Permanently delete token record for this device
            self::$tokenAuth->deleteToken($token);
        }
        
        // Clear cookie with same settings as when it was set
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        
        setcookie(
            'auth_token',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
        
        // Clear all authentication-related session variables
        $auth_keys = [
            'user_id', 'first_name', 'last_name', 'email', 'user_type', 
            'auth_fingerprint', 'user_email', 'user_name', 'welcome_shown',
            'auth_token', 'last_activity_log'
        ];
        
        foreach ($auth_keys as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }
        
        // Clear user data
        self::$user = null;
    }
    
    /**
     * Check if the current session is an admin in view-only mode
     * 
     * @return bool True if admin in view-only mode
     */
    public static function isAdminViewOnly(): bool {
        return isset($_SESSION['admin_view_only']) && $_SESSION['admin_view_only'] === true;
    }
    
    /**
     * Checks if the current action is allowed for admin view-only mode
     * Redirects to notification page if not allowed
     * 
     * @param bool $checkPostOnly If true, only checks POST requests
     * @return void
     */
    public static function guardAgainstAdminAction(bool $checkPostOnly = false): void {
        // If not in admin view mode or just GET request (when $checkPostOnly is true), allow
        if (!self::isAdminViewOnly() || ($checkPostOnly && $_SERVER['REQUEST_METHOD'] !== 'POST')) {
            return;
        }
        
        // Store the current URL for the redirect back
        $_SESSION['admin_redirect_from'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to admin notification page
        header('Location: /FYP/FYP/User/admin_notification.php');
        exit;
    }
}
?> 