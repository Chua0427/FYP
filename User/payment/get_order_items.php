<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Function to log messages
function log_message($level, $message): void {
    $log_dir = __DIR__ . '/logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, "$log_dir/order_items.log");
}

try {
    // Check if order_id is provided
    if (empty($_GET['order_id'])) {
        throw new Exception('Missing order ID parameter');
    }
    
    $order_id = htmlspecialchars(trim($_GET['order_id']));
    
    // Connect to database
    $db = new Database();
    
    // Check if order exists
    $order = $db->fetchOne(
        "SELECT * FROM orders WHERE order_id = ?", 
        [$order_id]
    );
    
    if (!$order) {
        throw new Exception("Order not found: $order_id");
    }
    
    // Get order items with product details
    $items = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.brand, p.product_img1 as product_img 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id
         WHERE oi.order_id = ?",
        [$order_id]
    );
    
    if (empty($items)) {
        log_message('WARNING', "No items found for order: $order_id");
    }
    
    // Return order items data
    echo json_encode([
        'success' => true,
        'items' => $items,
        'order' => $order
    ]);
    
    // Close database connection
    $db->close();
    
} catch (Exception $e) {
    log_message('ERROR', "Error fetching order items: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
}
?> 