<?php
declare(strict_types=1);

/**
 * CSRF protection utilities
 */

class CSRF
{
    /**
     * Generate a new CSRF token and store it in the session
     * 
     * @return string The generated token
     */
    public static function generateToken(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        return $token;
    }
    
    /**
     * Get the current CSRF token or generate a new one if it doesn't exist
     * 
     * @return string The current CSRF token
     */
    public static function getToken(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return self::generateToken();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate a submitted CSRF token against the stored one
     * 
     * @param string $token The token to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateToken(string $token): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Verify a submitted CSRF token, or terminate the script if invalid
     * Typically used in POST request handlers
     * 
     * @param string $token The token to verify
     * @param bool $regenerate Whether to regenerate the token after verification
     * @return void
     * @throws Exception If the token is invalid
     */
    public static function verifyToken(string $token, bool $regenerate = true): void
    {
        if (!self::validateToken($token)) {
            http_response_code(403);
            throw new Exception('Invalid CSRF token');
        }
        
        if ($regenerate) {
            self::generateToken();
        }
    }
    
    /**
     * Output a hidden input field with the CSRF token
     * 
     * @return string HTML for the hidden input field
     */
    public static function tokenField(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
} 