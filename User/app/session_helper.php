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
            session_start();
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