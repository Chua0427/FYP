<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/api_auth.php';

// Set content type to JSON
header('Content-Type: application/json');

// Verify user authentication
$user = requireApiAuth(); // This will exit with 401 if not authenticated
$user_id = $user['user_id'];

try {
    // Initialize Database
    $db = new Database();
    $db->beginTransaction();
    
    // Validate required parameters
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $product_size = trim($_POST['product_size'] ?? '');
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate input
    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    if (empty($product_size)) {
        throw new Exception('Product size is required');
    }
    
    if ($quantity <= 0) {
        throw new Exception('Quantity must be at least 1');
    }
    
    // Check if product exists
    $product = $db->fetchOne(
        "SELECT * FROM product WHERE product_id = ?", 
        [$product_id]
    );
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Check if stock is available
    $stock = $db->fetchOne(
        "SELECT * FROM stock WHERE product_id = ? AND product_size = ?", 
        [$product_id, $product_size]
    );
    
    if (!$stock || $stock['stock'] < $quantity) {
        throw new Exception('Insufficient stock available');
    }
    
    // Check if item already exists in cart
    $cart_item = $db->fetchOne(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND product_size = ?", 
        [$user_id, $product_id, $product_size]
    );
    
    if ($cart_item) {
        // Update quantity of existing item
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        // Check if new quantity is within stock limits
        if ($new_quantity > $stock['stock']) {
            throw new Exception('Cannot add more items. Stock limit reached.');
        }
        
        $db->execute(
            "UPDATE cart SET quantity = ? WHERE cart_id = ?", 
            [$new_quantity, $cart_item['cart_id']]
        );
        
        $message = 'Item quantity updated in your cart';
    } else {
        // Add new item to cart
        $db->execute(
            "INSERT INTO cart (user_id, product_id, product_size, quantity) VALUES (?, ?, ?, ?)", 
            [$user_id, $product_id, $product_size, $quantity]
        );
        
        $message = 'Item added to your cart';
    }
    
    // Commit transaction
    $db->commit();
    
    // Get updated cart count
    $cart_count = $db->fetchOne(
        "SELECT COUNT(*) as count FROM cart WHERE user_id = ?", 
        [$user_id]
    );
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'cart_count' => $cart_count ? $cart_count['count'] : 0
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
}
?> 