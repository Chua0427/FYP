<?php
declare(strict_types=1);

header('Content-Type: application/json');
// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../payment/db.php';
require_once __DIR__ . '/../app/init.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    // Fetch all cart items for user
    $cartItems = $db->fetchAll(
        "SELECT c.cart_id, c.quantity, p.product_name, p.product_img1, c.product_size,
                p.price, p.discount_price,
                CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
         FROM cart c
         JOIN product p ON c.product_id = p.product_id
         WHERE c.user_id = ?",
        [$user_id]
    );

    $totalItems = 0;
    $totalOriginal = 0.0;
    $totalPrice = 0.0;

    foreach ($cartItems as $item) {
        $qty = (int) $item['quantity'];
        $orig = (float) $item['price'];
        $final = (float) $item['final_price'];
        $totalItems += $qty;
        $totalOriginal += $orig * $qty;
        $totalPrice += $final * $qty;
    }
    $totalDiscount = $totalOriginal - $totalPrice;

    echo json_encode([
        'success' => true,
        'totalItems' => $totalItems,
        'totalOriginal' => $totalOriginal,
        'totalDiscount' => $totalDiscount,
        'totalPrice' => $totalPrice,
        'items' => $cartItems,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch cart summary']);
} 