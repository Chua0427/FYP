<?php

declare(strict_types=1);

/**
 * Admin Login Brute Force Protection
 * This class provides protection against brute force attacks by:
 * 1. Tracking failed login attempts by username and IP
 * 2. Blocking access after too many failed attempts
 * 3. Implementing progressive delays
 * 4. Logging suspicious activities
 */
class BruteForceProtection {
    private $pdo;
    private $max_attempts = 5;              // Maximum failed attempts before lockout
    private $lockout_time = 15 * 60;        // Initial lockout time in seconds (15 minutes)
    private $attempt_window = 60 * 60;      // Time window to count attempts (1 hour)
    private $progressive_lockout = true;    // Whether to increase lockout time progressively
    private $ip_blocking = true;            // Whether to block by IP in addition to username

    /**
     * Constructor initializes database connection
     */
    public function __construct() {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->ensureTableExists();
        } catch (PDOException $e) {
            error_log("BruteForceProtection initialization error: " . $e->getMessage());
            throw new Exception("Failed to initialize brute force protection");
        }
    }

    /**
     * Create the login_attempts table if it doesn't exist
     */
    private function ensureTableExists(): void {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            login_attempts_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            admin_email VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent VARCHAR(255),
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_successful TINYINT(1) DEFAULT 0,
            lockout_until TIMESTAMP NULL DEFAULT NULL,
            INDEX (admin_email),
            INDEX (ip_address)
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Failed to create login_attempts table: " . $e->getMessage());
            throw new Exception("Failed to set up brute force protection");
        }
    }

    /**
     * Record a failed login attempt
     * 
     * @param string $admin_email The admin email attempting to log in
     * @param string $ip_address The IP address of the client
     * @return void
     */
    public function recordFailedAttempt(string $admin_email, ?string $ip_address = null): void {
        if ($ip_address === null) {
            $ip_address = $this->getClientIP();
        }
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Calculate lockout time based on previous attempts
        $lockout_until = null;
        $attempts_count = $this->getAttemptCount($admin_email, $ip_address);
        
        // If we've reached max attempts, set a lockout
        if ($attempts_count >= $this->max_attempts - 1) {  // -1 because we're about to add another
            // Determine lockout duration (progressive or fixed)
            $lockout_duration = $this->lockout_time;
            
            if ($this->progressive_lockout) {
                // Calculate progressive lockout: base time * 2^(attempts_over_max)
                $lockout_multiplier = max(0, $attempts_count - $this->max_attempts + 1);
                $lockout_duration = $this->lockout_time * pow(2, $lockout_multiplier);
                
                // Cap at 24 hours
                $lockout_duration = min($lockout_duration, 86400); 
            }
            
            $lockout_until = date('Y-m-d H:i:s', (int) (time() + $lockout_duration));
        }
        
        try {
            $stmt = $this->pdo->prepare("INSERT INTO login_attempts 
                (admin_email, ip_address, user_agent, is_successful, lockout_until) 
                VALUES (:admin_email, :ip_address, :user_agent, 0, :lockout_until)");
                
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->bindParam(':lockout_until', $lockout_until);
            $stmt->execute();
            
            // Log suspicious activity if there are multiple failed attempts
            if ($attempts_count >= $this->max_attempts - 1) {
                $this->logSuspiciousActivity($admin_email, $ip_address, $attempts_count + 1);
            }
        } catch (PDOException $e) {
            error_log("Failed to record login attempt: " . $e->getMessage());
        }
    }

    /**
     * Record a successful login attempt
     * 
     * @param string $admin_email The admin email that successfully logged in
     * @param string $ip_address The IP address of the client
     * @return void
     */
    public function recordSuccessfulLogin(string $admin_email, ?string $ip_address = null): void {
        if ($ip_address === null) {
            $ip_address = $this->getClientIP();
        }
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        try {
            // Record the successful attempt
            $stmt = $this->pdo->prepare("INSERT INTO login_attempts 
                (admin_email, ip_address, user_agent, is_successful) 
                VALUES (:admin_email, :ip_address, :user_agent, 1)");
                
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Failed to record successful login: " . $e->getMessage());
        }
    }

    /**
     * Reset lockouts after successful login
     * 
     * @param string $admin_email The admin email to clear lockouts for
     * @param string $ip_address The IP address to clear lockouts for
     * @return void
     */
    public function clearLockouts(string $admin_email, ?string $ip_address = null): void {
        if ($ip_address === null) {
            $ip_address = $this->getClientIP();
        }
        
        try {
            // Reset the lockout for this admin email and IP
            $stmt = $this->pdo->prepare("UPDATE login_attempts 
                SET lockout_until = NULL 
                WHERE admin_email = :admin_email OR ip_address = :ip_address");
                
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Failed to clear lockouts: " . $e->getMessage());
        }
    }

    /**
     * Check if a user or IP is currently locked out
     * 
     * @param string $admin_email The admin email to check
     * @param string $ip_address The IP address to check (optional)
     * @return array Lockout info with 'is_locked' boolean and 'remaining_time' in seconds
     */
    public function isLockedOut(string $admin_email, ?string $ip_address = null): array {
        if ($ip_address === null) {
            $ip_address = $this->getClientIP();
        }
        
        $result = [
            'is_locked' => false,
            'remaining_time' => 0,
            'message' => ''
        ];
        
        try {
            // Check for active lockouts
            $stmt = $this->pdo->prepare("SELECT lockout_until 
                FROM login_attempts 
                WHERE (admin_email = :admin_email OR (:ip_blocking = 1 AND ip_address = :ip_address))
                AND lockout_until IS NOT NULL 
                AND lockout_until > NOW()
                ORDER BY lockout_until DESC
                LIMIT 1");
                
            $use_ip_blocking = $this->ip_blocking ? 1 : 0;
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':ip_blocking', $use_ip_blocking, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && isset($row['lockout_until'])) {
                $lockout_time = strtotime($row['lockout_until']);
                $current_time = time();
                
                if ($lockout_time > $current_time) {
                    $remaining_time = $lockout_time - $current_time;
                    
                    $minutes = ceil($remaining_time / 60);
                    
                    $result['is_locked'] = true;
                    $result['remaining_time'] = $remaining_time;
                    $result['message'] = "This account is temporarily locked. Please try again in $minutes minutes.";
                }
            }
        } catch (PDOException $e) {
            error_log("Failed to check lockout status: " . $e->getMessage());
        }
        
        return $result;
    }

    /**
     * Get the number of failed attempts for an admin email or IP
     * 
     * @param string $admin_email The admin email to check
     * @param string $ip_address The IP address to check
     * @return int Number of failed attempts within the time window
     */
    private function getAttemptCount(string $admin_email, string $ip_address): int {
        $count = 0;
        $window_start = date('Y-m-d H:i:s', time() - $this->attempt_window);
        
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as attempts 
                FROM login_attempts
                WHERE (admin_email = :admin_email OR (:ip_blocking = 1 AND ip_address = :ip_address))
                AND attempt_time > :window_start
                AND is_successful = 0");
                
            $use_ip_blocking = $this->ip_blocking ? 1 : 0;
            $stmt->bindParam(':admin_email', $admin_email);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':ip_blocking', $use_ip_blocking, PDO::PARAM_INT);
            $stmt->bindParam(':window_start', $window_start);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['attempts'] ?? 0;
        } catch (PDOException $e) {
            error_log("Failed to get attempt count: " . $e->getMessage());
        }
        
        return (int)$count;
    }

    /**
     * Get client's real IP address considering proxies
     * 
     * @return string The client IP address
     */
    private function getClientIP(): string {
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
     * Log suspicious login activity
     * 
     * @param string $admin_email The admin email being targeted
     * @param string $ip_address The IP address of the client
     * @param int $attempts Number of failed attempts
     */
    private function logSuspiciousActivity(string $admin_email, string $ip_address, int $attempts): void {
        // Log to application logs if Monolog is available
        if (isset($GLOBALS['authLogger'])) {
            $GLOBALS['authLogger']->warning('Possible brute force attack detected', [
                'admin_email' => $admin_email,
                'ip_address' => $ip_address,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'failed_attempts' => $attempts
            ]);
        } else {
            // Fallback to error_log
            error_log("SECURITY WARNING: Possible brute force attack on admin login. " .
                      "Admin email: $admin_email, IP: $ip_address, Attempts: $attempts");
        }
    }

    /**
     * Cleanup old login attempts records
     * Should be called periodically (e.g., via cron)
     * 
     * @param int $days Number of days to keep records for
     * @return int Number of records removed
     */
    public function cleanupOldAttempts(int $days = 30): int {
        $cutoff_date = date('Y-m-d H:i:s', time() - ($days * 86400));
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM login_attempts WHERE attempt_time < :cutoff_date");
            $stmt->bindParam(':cutoff_date', $cutoff_date);
            $stmt->execute();
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Failed to cleanup old login attempts: " . $e->getMessage());
            return 0;
        }
    }
} 