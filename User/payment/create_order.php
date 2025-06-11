<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

// Set content type to JSON
header('Content-Type: application/json');

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize request ID for logging
$request_id = uniqid('order_', true);

// Verify user authentication and CSRF token
try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'User not logged in']);
        exit;
    }
    
    $user_id = (int)$_SESSION['user_id'];

    // CSRF protection
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid security token. Please refresh the page and try again.']);
        exit;
    }

    // Initialize logger
    $logger = $GLOBALS['logger'];
    
    // Initialize Database
    $db = new Database();
    
    // Get shipping address from post data
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    
    // Store shipping address in session for use in payment process
    if (!empty($shipping_address)) {
        $_SESSION['checkout_shipping_address'] = $shipping_address;
    }
    
    // Get selected items if provided
    $selected_cart_ids = null;
    if (isset($_POST['selected_items'])) {
        $selected_items_json = $_POST['selected_items'];
        $selected_cart_ids = json_decode($selected_items_json, true);
        
        // Validate selected items
        if (!is_array($selected_cart_ids)) {
            throw new Exception('Invalid selected items format');
        }
        
        // Ensure all IDs are integers
        $selected_cart_ids = array_map('intval', $selected_cart_ids);
    }
    
    // Get cart items
    $cartItems = $db->fetchAll(
        "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
         CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
         FROM cart c 
         JOIN product p ON c.product_id = p.product_id 
         WHERE c.user_id = ?",
        [$user_id]
    );
    
    if (empty($cartItems)) {
        throw new Exception("Your cart is empty. Please add items before proceeding to checkout.");
    }
    
    // Store cart items in session
    $_SESSION['checkout_cart_items'] = $cartItems;
    
    // Calculate total price
    $totalPrice = 0;
    foreach ($cartItems as $item) {
        $totalPrice += $item['final_price'] * $item['quantity'];
    }
    $_SESSION['checkout_total_price'] = $totalPrice;
    
    // Log checkout step
    $logger->info('User proceeded to payment selection', [
        'user_id' => $user_id,
        'request_id' => $request_id
    ]);
    
    // Return success response with redirect to payment methods
    echo json_encode([
        'success' => true,
        'redirect_url' => '/FYP/User/payment/payment_methods.php'
    ]);
    exit;
    
} catch (Exception $e) {
    // Log the error if logger is available
    if (isset($logger)) {
        $logger->error('Order creation error', [
            'request_id' => $request_id,
            'error' => $e->getMessage(),
            'user_id' => $user_id ?? 'unknown'
        ]);
    }
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'request_id' => $request_id
    ]);
    exit;
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
} 