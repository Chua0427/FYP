<?php
/**
 * API Authentication functions
 */

require_once __DIR__ . '/auth.php';

/**
 * Verify API authentication
 * 
 * @return array Authentication result with user data or error
 */
function verifyApiAuth() {
    // Set content type to JSON
    header('Content-Type: application/json');
    
    // Check token authentication
    if (Auth::check()) {
        return [
            'authenticated' => true,
            'user' => Auth::user()
        ];
    }
    
    // Check session-based authentication (legacy)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate token for this user
                Auth::login($user['user_id'], $user);
                
                return [
                    'authenticated' => true,
                    'user' => $user
                ];
            }
        } catch (Exception $e) {
            error_log("API auth session validation error: " . $e->getMessage());
        }
    }
    
    // Authentication failed
    return [
        'authenticated' => false,
        'error' => 'Unauthorized'
    ];
}

/**
 * Require authentication for API endpoint
 * Returns 401 Unauthorized if not authenticated
 * 
 * @return array|null User data if authenticated, exits otherwise
 */
function requireApiAuth() {
    $auth = verifyApiAuth();
    
    if (!$auth['authenticated']) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized. Please login.'
        ]);
        exit;
    }
    
    return $auth['user'];
}
?> 