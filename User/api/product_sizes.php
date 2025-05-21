<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require_once __DIR__ . '/../app/init.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Validate product ID
    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
    
    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Initialize Database
    $db = new Database();
    
    // Get product information
    $product = $db->fetchOne(
        "SELECT * FROM product WHERE product_id = ?", 
        [$product_id]
    );
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Get available sizes with stock information
    $sizes = $db->fetchAll(
        "SELECT s.product_size, s.stock 
         FROM stock s 
         WHERE s.product_id = ? AND s.stock > 0
         ORDER BY s.product_size ASC", 
        [$product_id]
    );
    
    // Return success response
    echo json_encode([
        'success' => true,
        'product_id' => $product_id,
        'product_name' => htmlspecialchars($product['product_name']),
        'product_type' => htmlspecialchars($product['product_type']),
        'sizes' => $sizes
    ]);
    
} catch (Exception $e) {
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