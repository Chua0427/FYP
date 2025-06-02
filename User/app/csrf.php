<?php
declare(strict_types=1);

/**
 * CSRF Protection Functions
 * This file contains functions for Cross-Site Request Forgery (CSRF) protection.
 */

/**
 * Generate a CSRF token and store it in the session
 * @return string The generated CSRF token
 */
function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Generate a random token if it doesn't exist or is older than 1 hour
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        $_SESSION['csrf_token_time'] < (time() - 3600)) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token against the one stored in session
 * @param string $token The token to validate
 * @return bool True if the token is valid, false otherwise
 */
function validateCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Token is valid if it matches the one in session and isn't expired
    if (isset($_SESSION['csrf_token']) && 
        isset($_SESSION['csrf_token_time']) && 
        $_SESSION['csrf_token_time'] >= (time() - 3600)) {
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    return false;
}

/**
 * Output a hidden CSRF token input field for forms
 * @return string HTML for the CSRF token input field
 */
function csrfTokenField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Get the current CSRF token for use in AJAX requests
 * @return string The current CSRF token
 */
function getCsrfToken(): string
{
    return generateCsrfToken();
}

/**
 * Verify a submitted CSRF token, or terminate the script if invalid
 * Typically used in POST request handlers
 * @param string $token The token to verify
 * @param bool $regenerate Whether to regenerate the token after verification
 * @return void
 * @throws Exception If the token is invalid
 */
function verifyToken(string $token, bool $regenerate = true): void
{
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        throw new Exception('Invalid CSRF token');
    }
    
    if ($regenerate) {
        generateCsrfToken();
    }
} 