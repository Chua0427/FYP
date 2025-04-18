<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/services/OrderService.php';

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
    
    // Set transaction isolation level to SERIALIZABLE for strongest consistency
    $db->execute("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
    
    // Get shipping address from post data
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    
    // Create OrderService instance
    $orderService = new OrderService($db, $logger);
    
    // Create order with check_stock=true and additional request context
    $result = $orderService->createOrderFromCart(
        $user_id,
        $shipping_address,
        true, // Check stock
        ['request_id' => $request_id] // Additional context
    );
    
    // Add redirect URL to the result
    $result['redirect_url'] = '/FYP/FYP/User/payment/checkout.php?order_id=' . $result['order_id'];
    
    // Return success response
    echo json_encode($result);
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