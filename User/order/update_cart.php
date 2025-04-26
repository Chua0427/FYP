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
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$cart_id = (int)$_POST['cart_id'];
$quantity = max(1, (int)$_POST['quantity']);

try {
    // Initialize Database
    $db = new Database();
    $db->beginTransaction();
    
    // Get the cart item first to check stock availability and verify ownership
    $cartItem = $db->fetchOne(
        "SELECT c.*, s.stock FROM cart c
         JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
         WHERE c.cart_id = ? AND c.user_id = ?", 
        [$cart_id, $user_id]
    );
    
    if (!$cartItem) {
        throw new Exception("Cart item not found or does not belong to this user");
    }
    
    // Verify stock availability
    if ($quantity > $cartItem['stock']) {
        throw new Exception("Cannot update quantity. Only {$cartItem['stock']} items in stock.");
    }
    
    // Update quantity
    $db->execute(
        "UPDATE cart SET quantity = ?, added_at = CURRENT_TIMESTAMP WHERE cart_id = ? AND user_id = ?", 
        [$quantity, $cart_id, $user_id]
    );
    
    // Get updated item details for response
    $updatedItem = $db->fetchOne(
        "SELECT c.*, p.product_name, p.price, p.discount_price, 
         CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
         FROM cart c 
         JOIN product p ON c.product_id = p.product_id 
         WHERE c.cart_id = ?", 
        [$cart_id]
    );
    
    $db->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully',
        'item' => [
            'cart_id' => $cart_id,
            'quantity' => $quantity,
            'price' => $updatedItem ? (float)$updatedItem['final_price'] : 0,
            'total' => $updatedItem ? (float)$updatedItem['final_price'] * $quantity : 0
        ]
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    
    // Log the error
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->error('Cart update error', [
            'user_id' => $user_id,
            'cart_id' => $cart_id,
            'quantity' => $quantity,
            'error' => $e->getMessage()
        ]);
    }
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
} 