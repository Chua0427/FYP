<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
use Ramsey\Uuid\Uuid;

// Ensure logs directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Function to log messages
function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/payment.log');
}

// Set content type
header('Content-Type: application/json');

try {
    // Initialize Database
    $db = new Database();
    
    // Determine the action to take
    $action = $_GET['action'] ?? 'process';
    
    switch ($action) {
        case 'process':
            // Process a new payment or update an existing one
            processPayment($db);
            break;
            
        case 'status':
            // Get payment status
            getPaymentStatus($db);
            break;
            
        case 'verify':
            // Verify a payment (check with Stripe)
            verifyPayment($db);
            break;
            
        case 'list':
            // List all payments for an order or user
            listPayments($db);
            break;
            
        default:
            throw new Exception("Invalid action: $action");
    }
} catch (Exception $e) {
    log_message('ERROR', "Payment error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
}

/**
 * Process a payment request
 * 
 * @param Database $db Database connection
 * @return void
 */
function processPayment(Database $db): void {
    $db->beginTransaction();
    
    try {
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
        
        // If there's an existing payment, check its status
        if ($existing_payment) {
            // Skip creating new payment if already completed or processing
            if (in_array($existing_payment['payment_status'], ['completed', 'processing'])) {
                $db->commit();
                echo json_encode([
                    'success' => true,
                    'payment_id' => $existing_payment['payment_id'],
                    'status' => $existing_payment['payment_status'],
                    'message' => 'Payment already exists'
                ]);
                return;
            }
            
            // Use existing payment ID if available
            $payment_id = $existing_payment['payment_id'];
        } else {
            // Generate new payment ID
            $payment_id = Uuid::uuid4()->toString();
        }
        
        // Calculate total from order items
        $items = $db->fetchAll(
            "SELECT price, quantity FROM order_items WHERE order_id = ?", 
            [$order_id]
        );
        
        $calculated_total = 0;
        foreach ($items as $item) {
            $calculated_total += $item['price'] * $item['quantity'];
        }
        
        // Verify order total matches calculated total
        if (abs($calculated_total - $order['total_price']) > 0.01) {
            log_message('WARNING', "Total mismatch for order $order_id: Order total: {$order['total_price']}, Calculated: $calculated_total");
        }
        
        // Use order total
        $total_amount = $order['total_price'];
        
        // Store or update payment record
        if ($existing_payment) {
            // Update existing payment
            $db->execute(
                "UPDATE payment SET 
                 payment_status = 'pending',
                 total_amount = ?
                 WHERE payment_id = ?",
                [$total_amount, $payment_id]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                [$payment_id, "Payment record updated"]
            );
        } else {
            // Create new payment record
            $db->execute(
                "INSERT INTO payment 
                 (payment_id, order_id, total_amount, payment_status, payment_method) 
                 VALUES (?, ?, ?, 'pending', 'pending')",
                [$payment_id, $order_id, $total_amount]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                [$payment_id, "Payment record created"]
            );
        }
        
        // Commit transaction
        $db->commit();
        
        // Return success
        echo json_encode([
            'success' => true,
            'payment_id' => $payment_id,
            'order_id' => $order_id,
            'amount' => $total_amount,
            'status' => 'pending'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * Get payment status
 * 
 * @param Database $db Database connection
 * @return void
 */
function getPaymentStatus(Database $db): void {
    // Determine if we're looking up by order_id or payment_id
    $order_id = trim($_GET['order_id'] ?? '');
    $payment_id = trim($_GET['payment_id'] ?? '');
    
    if (empty($order_id) && empty($payment_id)) {
        throw new Exception('Missing order_id or payment_id');
    }
    
    // Fetch payment information
    $payment = null;
    if (!empty($payment_id)) {
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE payment_id = ?", 
            [$payment_id]
        );
    } else {
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1", 
            [$order_id]
        );
    }
    
    if (!$payment) {
        throw new Exception("Payment not found");
    }
    
    // Get payment logs
    $logs = $db->fetchAll(
        "SELECT * FROM payment_log WHERE payment_id = ? ORDER BY log_time DESC LIMIT 10",
        [$payment['payment_id']]
    );
    
    // Return payment status and details
    echo json_encode([
        'success' => true,
        'payment' => $payment,
        'logs' => $logs
    ]);
}

/**
 * Verify payment with Stripe
 * 
 * @param Database $db Database connection
 * @return void
 */
function verifyPayment(Database $db): void {
    \Stripe\Stripe::setApiKey($GLOBALS['stripeSecretKey']);
    
    $payment_id = trim($_GET['payment_id'] ?? '');
    
    if (empty($payment_id)) {
        throw new Exception('Missing payment_id');
    }
    
    // Get payment details
    $payment = $db->fetchOne(
        "SELECT * FROM payment WHERE payment_id = ?", 
        [$payment_id]
    );
    
    if (!$payment) {
        throw new Exception("Payment not found: $payment_id");
    }
    
    // Skip verification if no stripe_id
    if (empty($payment['stripe_id'])) {
        echo json_encode([
            'success' => true,
            'status' => $payment['payment_status'],
            'message' => 'No Stripe ID found for verification'
        ]);
        return;
    }
    
    // Check payment type and verify with Stripe
    if (strpos($payment['stripe_id'], 'cs_') === 0) {
        // This is a Checkout Session
        $session = \Stripe\Checkout\Session::retrieve($payment['stripe_id']);
        
        $verified_status = $session->payment_status;
        $stripe_status = ($verified_status == 'paid') ? 'completed' : 'pending';
        
        if ($stripe_status != $payment['payment_status']) {
            // Status mismatch, update our records
            $db->beginTransaction();
            
            try {
                // Update payment status
                $db->execute(
                    "UPDATE payment SET payment_status = ? WHERE payment_id = ?",
                    [$stripe_status, $payment_id]
                );
                
                // Log the change
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                    [$payment_id, "Payment status updated from {$payment['payment_status']} to $stripe_status via verification"]
                );
                
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
        
        echo json_encode([
            'success' => true,
            'status' => $stripe_status,
            'stripe_status' => $verified_status,
            'message' => 'Payment verified with Stripe'
        ]);
        
    } elseif (strpos($payment['stripe_id'], 'pi_') === 0) {
        // This is a Payment Intent
        $intent = \Stripe\PaymentIntent::retrieve($payment['stripe_id']);
        
        $verified_status = $intent->status;
        $stripe_status = 'pending';
        
        if (in_array($verified_status, ['succeeded', 'processing'])) {
            $stripe_status = ($verified_status == 'succeeded') ? 'completed' : 'processing';
        } elseif (in_array($verified_status, ['canceled', 'requires_payment_method'])) {
            $stripe_status = 'failed';
        }
        
        if ($stripe_status != $payment['payment_status']) {
            // Status mismatch, update our records
            $db->beginTransaction();
            
            try {
                // Update payment status
                $db->execute(
                    "UPDATE payment SET payment_status = ? WHERE payment_id = ?",
                    [$stripe_status, $payment_id]
                );
                
                // Log the change
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                    [$payment_id, "Payment status updated from {$payment['payment_status']} to $stripe_status via verification"]
                );
                
                $db->commit();
} catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
        
        echo json_encode([
            'success' => true,
            'status' => $stripe_status,
            'stripe_status' => $verified_status,
            'message' => 'Payment verified with Stripe'
        ]);
        
    } else {
        // Unknown Stripe ID format
        echo json_encode([
            'success' => false,
            'error' => 'Unknown Stripe ID format: ' . $payment['stripe_id']
        ]);
    }
}

/**
 * List payments for an order or user
 * 
 * @param Database $db Database connection
 * @return void
 */
function listPayments(Database $db): void {
    $order_id = trim($_GET['order_id'] ?? '');
    $user_id = trim($_GET['user_id'] ?? '');
    
    if (empty($order_id) && empty($user_id)) {
        throw new Exception('Missing order_id or user_id');
    }
    
    $payments = [];
    
    if (!empty($order_id)) {
        // Get payments for a specific order
        $payments = $db->fetchAll(
            "SELECT p.*, 
             (SELECT COUNT(*) FROM payment_log WHERE payment_id = p.payment_id) as log_count 
             FROM payment p 
             WHERE p.order_id = ? 
             ORDER BY p.payment_at DESC",
            [$order_id]
        );
    } else {
        // Get payments for a specific user
        $payments = $db->fetchAll(
            "SELECT p.*, o.total_price, o.delivery_status,
             (SELECT COUNT(*) FROM payment_log WHERE payment_id = p.payment_id) as log_count 
             FROM payment p 
             JOIN orders o ON p.order_id = o.order_id
             WHERE o.user_id = ? 
             ORDER BY p.payment_at DESC 
             LIMIT 50",
            [$user_id]
        );
    }
    
    echo json_encode([
        'success' => true,
        'payments' => $payments
    ]);
}
?>
