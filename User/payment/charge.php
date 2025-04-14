<?php

declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

ini_set('error_log', __DIR__ . '/logs/error.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);


function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/payment.log');
}

header('Content-Type: application/json');

try {
    $db = new Database();
    $db->beginTransaction();
    
    \Stripe\Stripe::setApiKey($stripeSecretKey);
    
    $order_id = trim($_POST['order_id'] ?? '');
    $payment_method_id = trim($_POST['payment_method_id'] ?? '');
    
    if (empty($order_id)) {
        throw new Exception('Missing order ID');
    }
    
    if (empty($payment_method_id)) {
        throw new Exception('Invalid payment method ID');
    }
    
    $order = $db->fetchOne("SELECT * FROM orders WHERE order_id = ?", [$order_id]);
    if (!$order) {
        throw new Exception("Order not found: $order_id");
    }
    
    $existing_payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ?", 
        [$order_id]
    );
    
    if ($existing_payment) {
        if ($existing_payment['payment_status'] === 'completed') {
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
        
        $db->execute(
            "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
            [$existing_payment['payment_id'], "Retrying payment with new payment method"]
        );
        
    }
    
    $user_id = $order['user_id'];
    $total_price = $order['total_price'];
    $amount = (int) round($total_price * 100);
    
    $items = $db->fetchAll(
        "SELECT price, quantity FROM order_items WHERE order_id = ?", 
        [$order_id]
    );
    
    $calculated_total = 0;
    foreach ($items as $item) {
        $calculated_total += $item['price'] * $item['quantity'];
    }
    
    if (abs($calculated_total - $total_price) > 0.01) {
        log_message('WARNING', "Total mismatch for order $order_id: Order total: $total_price, Calculated: $calculated_total");
    }
    
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'payment_method' => $payment_method_id,
        'currency' => 'myr',
        'metadata' => [
            'order_id' => $order_id,
            'user_id' => $user_id
        ],
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never'
        ],
        'confirm' => true,
        'return_url' => "http://{$_SERVER['HTTP_HOST']}/FYP/User/payment/success.php?order_id=$order_id"
    ]);
    
    // Generate a unique payment ID without UUID
    $payment_id = 'PAY-' . strtoupper(bin2hex(random_bytes(12)));
    
    $payment_status = 'pending';
    if ($paymentIntent->status === 'succeeded') {
        $payment_status = 'completed';
    } elseif ($paymentIntent->status === 'requires_action') {
        $payment_status = 'pending';
    } elseif (in_array($paymentIntent->status, ['canceled', 'requires_payment_method'])) {
        $payment_status = 'failed';
    }
    
    // Convert order_id to string for payment table
    $order_id_str = (string)$order_id;
    
    $db->execute(
        "INSERT INTO payment 
         (payment_id, order_id, total_amount, payment_status, payment_method, stripe_id, currency, stripe_created) 
         VALUES (?, ?, ?, ?, 'stripe', ?, 'MYR', ?)",
        [$payment_id, $order_id_str, $total_price, $payment_status, $paymentIntent->id, $paymentIntent->created]
    );
    
    $db->execute(
        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
        [$payment_id, "Payment initiated with Stripe: {$paymentIntent->id}, Status: {$paymentIntent->status}"]
    );
    
    if ($payment_status === 'completed') {
        $db->execute(
            "UPDATE orders SET delivery_status = 'packing' WHERE order_id = ?",
            [$order_id]
        );
    }
    
    $db->commit();
    
    log_message('INFO', "Payment processed for Order ID: $order_id | Stripe ID: {$paymentIntent->id} | Status: {$paymentIntent->status}");
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'payment_id' => $payment_id,
        'stripe_id' => $paymentIntent->id,
        'status' => $payment_status,
        'requires_action' => $paymentIntent->status === 'requires_action',
        'client_secret' => $paymentIntent->status === 'requires_action' ? $paymentIntent->client_secret : null,
        'next_action' => $paymentIntent->status === 'requires_action' ? $paymentIntent->next_action : null
    ]);

} catch (\Stripe\Exception\CardException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    $error_message = $e->getMessage();
    log_message('ERROR', "Card error: " . $error_message);
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Card declined: ' . htmlspecialchars($error_message), 
        'code' => $e->getStripeCode()
    ]);
} catch (\Stripe\Exception\RateLimitException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Rate limit error: " . $e->getMessage());
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests, please try again later.']);
} catch (\Stripe\Exception\InvalidRequestException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Invalid request: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payment request: ' . htmlspecialchars($e->getMessage())]);
} catch (\Stripe\Exception\AuthenticationException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Authentication error: " . $e->getMessage());
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Payment system configuration error.']);
} catch (\Stripe\Exception\ApiConnectionException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "API connection error: " . $e->getMessage());
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Network error connecting to payment processor.']);
} catch (\Stripe\Exception\ApiErrorException $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('STRIPE_ERROR', $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Stripe API error: ' . htmlspecialchars($e->getMessage())]);
} catch (Exception $e) {
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    log_message('ERROR', "Payment failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => htmlspecialchars($e->getMessage())]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}

?>
