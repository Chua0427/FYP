<?php
declare(strict_types=1);

/**
 * Token utility functions for authentication
 */

class TokenAuth {
    private $db;
    private $token_expiry = 2592000; // 30 days in seconds

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
     * @param int|null $expirySeconds Expiry in seconds (null for default, 86400 for 1 day, -1 for infinite)
     * @return string Generated token
     * @throws Exception If token generation fails
     */
    public function generateToken(int $user_id, ?int $expirySeconds = null): string {
        try {
            // Generate a random token with better entropy (Split-token approach)
            $token_id = bin2hex(random_bytes(16)); // Public identifier
            $token_secret = bin2hex(random_bytes(32)); // Secret part
            $token = $token_id . '.' . $token_secret; // Combined token for client
            
            // Hash the secret part for storage (we never store the raw secret)
            $token_hash = hash('sha256', $token_secret);
            
            // Determine expiry
            if ($expirySeconds === null) {
                $expirySeconds = $this->token_expiry;
            }
            if ($expirySeconds < 0) {
                // Infinite expiry
                $expires_at = '9999-12-31 23:59:59';
            } else {
                $expires_at = date('Y-m-d H:i:s', time() + $expirySeconds);
            }
            
            // Get browser and device info for logging
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $ip_address = $this->getClientIP();
            
            // Store token in database with prepared statement and user agent info
            $stmt = $this->db->prepare("INSERT INTO user_tokens 
                                        (user_id, token, expires_at, created_at, user_agent, ip_address) 
                                        VALUES (:user_id, :token, :expires_at, NOW(), :user_agent, :ip_address)");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token_hash, PDO::PARAM_STR); // Store the hashed secret, not the full token
            $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
            $stmt->execute();
            
            return $token; // Return the full token to the client
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
            // Split token into id and secret parts
            $token_parts = explode('.', $token);
            if (count($token_parts) !== 2) {
                return false; // Invalid token format
            }
            
            $token_id = $token_parts[0];
            $token_secret = $token_parts[1];
            
            // Hash the secret for comparison
            $token_hash = hash('sha256', $token_secret);
            
            // Check if token exists and is not expired
            $stmt = $this->db->prepare("
                SELECT u.*, t.token_id, t.token, t.expires_at, t.user_agent 
                FROM user_tokens t
                JOIN users u ON t.user_id = u.user_id
                WHERE t.token = :token_hash AND t.expires_at > NOW() AND t.is_revoked = 0
                LIMIT 1
            ");
            
            $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Update last access time for this token
                $this->updateTokenLastAccess($token_hash);
                return $result;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Token Validation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update token's last access timestamp
     * 
     * @param string $token_hash Hashed token to update
     * @return bool Success status
     */
    private function updateTokenLastAccess(string $token_hash): bool {
        try {
            $stmt = $this->db->prepare("UPDATE user_tokens SET last_used_at = NOW() WHERE token = :token_hash");
            $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Token Update Error: " . $e->getMessage());
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
     * Get all active tokens for a user (useful for showing logged-in devices)
     * 
     * @param int $user_id User ID
     * @return array List of active tokens with metadata
     */
    public function getUserTokens(int $user_id): array {
        try {
            // Include token in result to identify current session
            $stmt = $this->db->prepare("SELECT token_id, token, created_at, expires_at, last_used_at, user_agent, ip_address
                FROM user_tokens 
                WHERE user_id = :user_id 
                AND expires_at > NOW() 
                AND is_revoked = 0
                ORDER BY last_used_at DESC");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get User Tokens Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Revoke a specific token
     * 
     * @param string $token Token to revoke
     * @return bool Success status
     */
    public function revokeToken(string $token): bool {
        try {
            // Split token into id and secret parts
            $token_parts = explode('.', $token);
            if (count($token_parts) !== 2) {
                return false; // Invalid token format
            }
            
            $token_secret = $token_parts[1];
            $token_hash = hash('sha256', $token_secret);
            
            $stmt = $this->db->prepare("UPDATE user_tokens SET is_revoked = 1 WHERE token = :token_hash");
            $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Token Revocation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Revoke a token by its ID
     * 
     * @param int $token_id Token ID to revoke
     * @param int $user_id User ID for verification
     * @return bool Success status
     */
    public function revokeTokenById(int $token_id, int $user_id): bool {
        try {
            $stmt = $this->db->prepare("UPDATE user_tokens SET is_revoked = 1 
                                       WHERE token_id = :token_id AND user_id = :user_id");
            $stmt->bindParam(':token_id', $token_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
     * Revoke all tokens for a user except the current one
     * 
     * @param int $user_id User ID
     * @param string $current_token Current token to keep
     * @return bool Success status
     */
    public function revokeOtherUserTokens(int $user_id, string $current_token): bool {
        try {
            // Split token into id and secret parts
            $token_parts = explode('.', $current_token);
            if (count($token_parts) !== 2) {
                return false; // Invalid token format
            }
            
            $token_secret = $token_parts[1];
            $token_hash = hash('sha256', $token_secret);
            
            $stmt = $this->db->prepare("UPDATE user_tokens 
                                       SET is_revoked = 1 
                                       WHERE user_id = :user_id 
                                       AND token != :token_hash");
                                       
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Token Revocation Error: " . $e->getMessage());
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

    /**
     * Get the client's real IP address
     * 
     * @return string Client IP address
     */
    private function getClientIP(): string {
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

    /**
     * Permanently delete a specific token record
     *
     * @param string $token Token to delete
     * @return bool Success status
     */
    public function deleteToken(string $token): bool {
        try {
            // Split token into id and secret parts
            $token_parts = explode('.', $token);
            if (count($token_parts) !== 2) {
                return false; // Invalid token format
            }
            
            $token_secret = $token_parts[1];
            $token_hash = hash('sha256', $token_secret);
            
            $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE token = :token_hash");
            $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Token Deletion Error: " . $e->getMessage());
            return false;
        }
    }
}
?> 