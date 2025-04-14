<?php
declare(strict_types=1);

/**
 * Token utility functions for authentication
 */

class TokenAuth {
    private $db;
    private $token_expiry = 86400; // 24 hours in seconds

    /**
     * Constructor
     * 
     * @throws Exception If database connection fails
     */
    public function __construct() {
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("TokenAuth DB Connection Error: " . $e->getMessage());
            throw new Exception("Database connection error");
        }
    }

    /**
     * Generate a new authentication token
     * 
     * @param int $user_id User ID
     * @return string Generated token
     * @throws Exception If token generation fails
     */
    public function generateToken(int $user_id): string {
        try {
            // Generate a random token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', time() + $this->token_expiry);
            
            // Store token in database with prepared statement
            $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, token, expires_at, created_at) 
                                        VALUES (:user_id, :token, :expires_at, NOW())");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
            $stmt->execute();
            
            return $token;
        } catch (Exception $e) {
            error_log("Token Generation Error: " . $e->getMessage());
            throw new Exception("Failed to generate authentication token");
        }
    }

    /**
     * Validate a token
     * 
     * @param string $token Token to validate
     * @return array|bool User data if valid, false otherwise
     */
    public function validateToken(string $token) {
        try {
            // Check if token exists and is not expired
            $stmt = $this->db->prepare("
                SELECT u.*, t.token, t.expires_at 
                FROM user_tokens t
                JOIN users u ON t.user_id = u.user_id
                WHERE t.token = :token AND t.expires_at > NOW() AND t.is_revoked = 0
                LIMIT 1
            ");
            
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Token Validation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse token from various sources (cookie, header)
     * 
     * @return string|null Token if found, null otherwise
     */
    public function parseToken(): ?string {
        // Check in cookie first (most common)
        if (isset($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }
        
        // Check in Authorization header (for API requests)
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    /**
     * Revoke a specific token
     * 
     * @param string $token Token to revoke
     * @return bool Success status
     */
    public function revokeToken(string $token): bool {
        try {
            $stmt = $this->db->prepare("UPDATE user_tokens SET is_revoked = 1 WHERE token = :token");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Token Revocation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Revoke all tokens for a user
     * 
     * @param int $user_id User ID
     * @return bool Success status
     */
    public function revokeAllUserTokens(int $user_id): bool {
        try {
            $stmt = $this->db->prepare("UPDATE user_tokens SET is_revoked = 1 WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("User Token Revocation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired tokens (can be run periodically)
     * 
     * @return void
     */
    public function cleanupExpiredTokens(): void {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Token Cleanup Error: " . $e->getMessage());
        }
    }
}
?> 