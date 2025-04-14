<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require __DIR__ . '/../app/init.php';

header('Content-Type: application/json');

try {
    // Validate required parameters
    if (empty($_GET['order_id'])) {
        throw new Exception("Missing order ID parameter");
    }
    
    $order_id = htmlspecialchars(trim($_GET['order_id']));
    
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception("Order not found");
    }
    
    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT oi.*, p.product_name, p.product_img1 as product_img 
        FROM order_items oi
        JOIN product p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the order details and items
    echo json_encode([
        'success' => true,
        'order' => $order,
        'order_items' => $order_items
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 