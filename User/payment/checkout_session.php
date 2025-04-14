<?php

declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

// Removed Ramsey\Uuid dependency

// Ensure logs directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Function to log messages
function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/checkout.log');
}

// Set content type
header('Content-Type: application/json');

try {
    // Initialize Database
    $db = new Database();
    $db->beginTransaction();
    
    // Initialize Stripe
    \Stripe\Stripe::setApiKey($stripeSecretKey);
    
    // Validate required parameters
    $order_id = trim($_POST['order_id'] ?? '');
    
    if (empty($order_id)) {
        throw new Exception('Missing order ID');
    }
    
    // Get the order from database
    $order = $db->fetchOne("SELECT * FROM orders WHERE order_id = ?", [$order_id]);
    if (!$order) {
        throw new Exception("Order not found: $order_id");
    }
    
    // Check if payment already exists
    $existing_payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ?", 
        [$order_id]
    );
    
    // If payment already exists and is successful, return that info
    if ($existing_payment && $existing_payment['payment_status'] === 'completed') {
        $db->rollback();
        echo json_encode([
            'success' => true,
            'payment_id' => $existing_payment['payment_id'],
            'stripe_id' => $existing_payment['stripe_id'],
            'status' => $existing_payment['payment_status'],
            'message' => 'Payment already processed'
        ]);
        exit;
    }
    
    // Get order details
    $user_id = $order['user_id'];
    $total_price = $order['total_price'];
    $amount = (int) round($total_price * 100);
    
    // Get order items for line items
    $items = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.product_img1 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?",
        [$order_id]
    );
    
    // User info
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE user_id = ?",
        [$user_id]
    );
    
    // Format line items for Stripe
    $line_items = [];
    foreach ($items as $item) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item['product_name'],
                    'images' => [!empty($item['product_img1']) ? "http://{$_SERVER['HTTP_HOST']}/FYP" . $item['product_img1'] : null],
                    'metadata' => [
                        'product_id' => $item['product_id'],
                        'product_size' => $item['product_size']
                    ]
                ],
                'unit_amount' => (int) round($item['price'] * 100),
            ],
            'quantity' => $item['quantity'],
        ];
    }
    
    // Convert order_id to string for metadata and URLs
    $order_id_str = (string)$order_id;
    
    // Create a Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'customer_email' => $user['email'] ?? null,
        'success_url' => "http://{$_SERVER['HTTP_HOST']}/FYP/User/payment/success.php?session_id={CHECKOUT_SESSION_ID}&order_id=$order_id_str",
        'cancel_url' => "http://{$_SERVER['HTTP_HOST']}/FYP/User/payment/cancel.php?order_id=$order_id_str",
        'metadata' => [
            'order_id' => $order_id_str,
            'user_id' => $user_id
        ],
    ]);
    
    // Generate payment ID
    $payment_id = 'PAY-' . strtoupper(bin2hex(random_bytes(12)));
    
    // Insert payment record with Checkout Session ID
    $db->execute(
        "INSERT INTO payment 
         (payment_id, order_id, total_amount, payment_status, payment_method, stripe_id, currency) 
         VALUES (?, ?, ?, 'pending', 'stripe_checkout', ?, 'MYR')",
        [$payment_id, $order_id_str, $total_price, $checkout_session->id]
    );
    
    // Add log entry
    $db->execute(
        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
        [$payment_id, "Checkout session created: {$checkout_session->id}"]
    );
    
    // Commit transaction
    $db->commit();
    
    // Log successful creation
    log_message('INFO', "Checkout session created for Order ID: $order_id | Session ID: {$checkout_session->id}");
    
    // Return response to client
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'payment_id' => $payment_id,
        'checkout_session_id' => $checkout_session->id,
        'checkout_url' => $checkout_session->url
    ]);

} catch (\Stripe\Exception\CardException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Card error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Card declined: ' . htmlspecialchars($e->getMessage())]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Stripe API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Stripe API error: ' . htmlspecialchars($e->getMessage())]);
} catch (Exception $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Checkout error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => htmlspecialchars($e->getMessage())]);
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
}
?> 