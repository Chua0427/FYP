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
        
        // If user is already loaded, return true
        if (self::$user) {
            return true;
        }
        
        // Parse token from request
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
     * Login a user and generate a token
     * 
     * @param int $user_id User ID
     * @param array $user_data User data to store
     * @param bool $remember Whether to set a long-lived cookie
     * @return string Generated token
     */
    public static function login(int $user_id, array $user_data, bool $remember = false): string {
        self::init();
        
        // Generate token
        $token = self::$tokenAuth->generateToken($user_id);
        
        // Store user data
        self::$user = $user_data;
        
        // Set cookie with HttpOnly and secure flags
        $expiry = $remember ? time() + (86400 * 30) : 0; // 30 days if remember, otherwise session cookie
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        
        setcookie(
            'auth_token',
            $token,
            [
                'expires' => $expiry,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
        
        return $token;
    }
    
    /**
     * Logout current user
     * 
     * @return void
     */
    public static function logout(): void {
        self::init();
        
        // Get token
        $token = self::$tokenAuth->parseToken();
        
        // Revoke token if exists
        if ($token) {
            self::$tokenAuth->revokeToken($token);
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
        
        // Clear user data
        self::$user = null;
    }
}
?> 