<?php
declare(strict_types=1);

// Create this file at: /FYP/FYP/User/api/get_cart_data.php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require __DIR__ . '/../app/init.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Initialize session if not already started
ensure_session_started();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Initialize Database
    $db = new Database();
    
    // Remove cart entries for products marked as deleted
    $db->execute(
        "DELETE c FROM cart c JOIN product p ON c.product_id = p.product_id WHERE p.deleted = 1 AND c.user_id = ?",
        [$user_id]
    );
    
    // Check if this is for checkout page
    $isCheckout = isset($_GET['checkout']) && $_GET['checkout'] === '1';
    
    // If checkout and we have selected items in session, only include those
    if ($isCheckout && isset($_SESSION['selected_cart_items'])) {
        $selectedItems = $_SESSION['selected_cart_items'];
        
        if (!empty($selectedItems)) {
            $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
            $params = array_merge([$user_id], $selectedItems);
            
            $cartItems = $db->fetchAll(
                "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
                 CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price,
                 s.stock
                 FROM cart c
                 JOIN product p ON c.product_id = p.product_id
                 JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
                 WHERE c.user_id = ? AND c.cart_id IN ($placeholders) AND p.deleted = 0
                 ORDER BY p.brand, c.added_at DESC",
                $params
            );
        } else {
            // No items selected, return empty array
            $cartItems = [];
        }
    } else {
        // Regular cart view - fetch all items
        $cartItems = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price,
             s.stock
             FROM cart c
             JOIN product p ON c.product_id = p.product_id
             JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
             WHERE c.user_id = ? AND p.deleted = 0
             ORDER BY p.brand, c.added_at DESC",
            [$user_id]
        );
    }
    
    // Calculate totals
    $totalItems = 0;
    $totalPrice = 0;
    $totalOriginalPrice = 0;
    
    foreach ($cartItems as $item) {
        $totalItems += (int)$item['quantity'];
        $totalPrice += (float)$item['final_price'] * (int)$item['quantity'];
        $totalOriginalPrice += (float)$item['price'] * (int)$item['quantity'];
    }
    
    $totalDiscount = $totalOriginalPrice - $totalPrice;
    
    // Format items for response
    $formattedItems = array_map(function($item) {
        return [
            'cart_id' => (int)$item['cart_id'],
            'product_id' => (int)$item['product_id'],
            'product_name' => $item['product_name'],
            'product_size' => $item['product_size'],
            'quantity' => (int)$item['quantity'],
            'price' => (float)$item['price'],
            'discount_price' => $item['discount_price'] !== null ? (float)$item['discount_price'] : null,
            'final_price' => (float)$item['final_price'],
            'product_img1' => $item['product_img1'],
            'brand' => $item['brand'],
            'stock' => (int)$item['stock'],
            'item_total' => (float)($item['final_price'] * $item['quantity']),
            'item_original_total' => (float)($item['price'] * $item['quantity']),
        ];
    }, $cartItems);
    
    echo json_encode([
        'success' => true,
        'items' => $formattedItems,
        'summary' => [
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
            'totalOriginalPrice' => $totalOriginalPrice,
            'totalDiscount' => $totalDiscount
        ]
    ]);
    
} catch (Exception $e) {
    // Log the error
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->error('Failed to fetch cart data', [
            'user_id' => $user_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to fetch cart data: ' . $e->getMessage()
    ]);
}
?> 