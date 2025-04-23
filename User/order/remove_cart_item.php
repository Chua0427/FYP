<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

// For AJAX requests
header('Content-Type: application/json');

session_start();

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
        "SELECT * FROM cart WHERE cart_id = ? AND user_id = ?", 
        [$cart_id, $user_id]
    );
    
    if (!$cartItem) {
        throw new Exception("Cart item not found or does not belong to this user");
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
    
    // Get updated cart count after removal
    $cartCount = $db->fetchOne(
        "SELECT COUNT(*) as count FROM cart WHERE user_id = ?", 
        [$user_id]
    );
    
    $db->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => $cartCount ? (int)$cartCount['count'] : 0
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    
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