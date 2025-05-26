<?php
declare(strict_types=1);

// Clear any previous output and restart buffer
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

// For AJAX requests
header('Content-Type: application/json');
// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Initialize session if not already started
ensure_session_started();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

// Validate required parameters
if (!isset($_POST['cart_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing cart_id parameter']);
    exit;
}

$cart_id = (int)$_POST['cart_id'];

try {
    // Initialize Database
    $db = new Database();
    $db->beginTransaction();
    
    // Verify cart item belongs to this user before deleting
    $cartItem = $db->fetchOne(
        "SELECT c.* FROM cart c WHERE c.cart_id = ? AND c.user_id = ?", 
        [$cart_id, $user_id]
    );
    
    if (!$cartItem) {
        // Log the failed item lookup
        $GLOBALS['logger']->warning('Cart item not found during removal attempt', [
            'user_id' => $user_id,
            'cart_id' => $cart_id,
            'session_id' => session_id()
        ]);
        
        // Try to find if the item existed but doesn't belong to this user
        $anyItem = $db->fetchOne(
            "SELECT user_id FROM cart WHERE cart_id = ?", 
            [$cart_id]
        );
        
        if ($anyItem) {
            throw new Exception("Cart item belongs to another user account");
        } else {
            // Check database connection to ensure it's not a DB issue
            try {
                $count = $db->fetchOne("SELECT COUNT(*) as count FROM cart", []);
                $GLOBALS['logger']->info('DB connection check during cart removal failure', [
                    'total_cart_items' => $count ? $count['count'] : 'unknown',
                    'cart_id' => $cart_id,
                    'user_id' => $user_id
                ]);
            } catch (Exception $dbEx) {
                $GLOBALS['logger']->error('DB connection issue during cart removal', [
                    'error' => $dbEx->getMessage()
                ]);
                throw new Exception("Database connection issue - please try again");
            }
            
            // Return explicit information if cart item not found
            throw new Exception("Cart item not found - it may have been already deleted");
        }
    }
    
    // Log attempt to remove item
    $GLOBALS['logger']->info('Removing cart item', [
        'user_id' => $user_id,
        'cart_id' => $cart_id
    ]);
    
    // Delete the cart item
    $db->execute(
        "DELETE FROM cart WHERE cart_id = ? AND user_id = ?", 
        [$cart_id, $user_id]
    );
    
    // Get updated cart total quantity after removal
    $cartCount = $db->fetchOne(
        "SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?", 
        [$user_id]
    );
    
    // Clear any stored checkout session data so summaries reload correctly
    unset(
        $_SESSION['checkout_cart_items'],
        $_SESSION['checkout_total_price']
    );
    
    $db->commit();
    
    // Return success response
    try {
        $response = [
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount ? (int)$cartCount['count'] : 0
        ];
        echo json_encode($response);
    } catch (Exception $jsonEx) {
        // Log JSON error
        if (isset($GLOBALS['logger'])) {
            $GLOBALS['logger']->error('JSON encoding error', [
                'error' => $jsonEx->getMessage()
            ]);
        }
        // Fallback response
        echo '{"success":true,"message":"Item removed from cart","cart_count":0}';
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    http_response_code(400);
    
    // Ensure clean JSON error response
    try {
        $errorResponse = [
            'success' => false, 
            'error' => $e->getMessage()
        ];
        echo json_encode($errorResponse);
    } catch (Exception $jsonEx) {
        // Fallback plain error if JSON encoding fails
        echo '{"success":false,"error":"An error occurred while processing your request"}';
    }
    
    // Log the error
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->error('Cart item removal error', [
            'user_id' => $user_id,
            'cart_id' => $cart_id,
            'error' => $e->getMessage()
        ]);
    }
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
} 