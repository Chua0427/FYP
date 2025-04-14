<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require __DIR__ . '/../app/init.php';

// Set up Stripe
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Set up logging
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/webhook_checkout.log');
}

// Get the webhook endpoint secret from secrets.php
$webhook_secret = $stripeWebhookSecret ?? '';
if (empty($webhook_secret)) {
    log_message('ERROR', 'Webhook secret is not set in secrets.php');
    http_response_code(500);
    exit();
}

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    // Verify the event came from Stripe
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
    
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle the event
    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;
            handleCheckoutSessionCompleted($pdo, $session);
            break;
            
        case 'checkout.session.expired':
            $session = $event->data->object;
            handleCheckoutSessionExpired($pdo, $session);
            break;
            
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            handlePaymentIntentSucceeded($pdo, $paymentIntent);
            break;
            
        case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;
            handlePaymentIntentFailed($pdo, $paymentIntent);
            break;
            
        default:
            log_message('INFO', "Unhandled event type: {$event->type}");
    }
    
    http_response_code(200);
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    log_message('ERROR', "Invalid payload: {$e->getMessage()}");
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    log_message('ERROR', "Invalid signature: {$e->getMessage()}");
    http_response_code(400);
    exit();
} catch (Exception $e) {
    // Other errors
    log_message('ERROR', "Error: {$e->getMessage()}");
    http_response_code(500);
    exit();
}

/**
 * Handle successful checkout completion
 */
function handleCheckoutSessionCompleted($pdo, $session) {
    try {
        $pdo->beginTransaction();
        
        // Get session ID
        $sessionId = $session->id;
        
        // Find the payment record by the session ID
        $stmt = $pdo->prepare("SELECT * FROM payment WHERE stripe_id = ?");
        $stmt->execute([$sessionId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            throw new Exception("Payment not found for Checkout Session: $sessionId");
        }
        
        $payment_id = $payment['payment_id'];
        $order_id = $payment['order_id'];
        
        // Update payment status
        $stmt = $pdo->prepare("UPDATE payment 
                              SET payment_status = 'completed', 
                                  stripe_created = ? 
                              WHERE payment_id = ?");
        $stmt->execute([$session->created, $payment_id]);
        
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders 
                              SET delivery_status = 'packing' 
                              WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        // Log the payment success
        $stmt = $pdo->prepare("INSERT INTO payment_log 
                              (payment_id, log_level, log_message) 
                              VALUES (?, 'info', ?)");
        $log_message = "Checkout payment completed: " . $sessionId;
        $stmt->execute([$payment_id, $log_message]);
        
        $pdo->commit();
        log_message('INFO', "Payment completed for Order ID: $order_id | Session ID: $sessionId");
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_message('ERROR', "Error processing checkout.session.completed: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle expired checkout sessions
 */
function handleCheckoutSessionExpired($pdo, $session) {
    try {
        $pdo->beginTransaction();
        
        // Get session ID
        $sessionId = $session->id;
        
        // Find the payment record by the session ID
        $stmt = $pdo->prepare("SELECT * FROM payment WHERE stripe_id = ?");
        $stmt->execute([$sessionId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            throw new Exception("Payment not found for Checkout Session: $sessionId");
        }
        
        $payment_id = $payment['payment_id'];
        $order_id = $payment['order_id'];
        
        // Update payment status
        $stmt = $pdo->prepare("UPDATE payment 
                              SET payment_status = 'failed',
                                  stripe_created = ? 
                              WHERE payment_id = ?");
        $stmt->execute([$session->created, $payment_id]);
        
        // Log the payment expiration
        $stmt = $pdo->prepare("INSERT INTO payment_log 
                              (payment_id, log_level, log_message) 
                              VALUES (?, 'warning', ?)");
        $log_message = "Checkout session expired: " . $sessionId;
        $stmt->execute([$payment_id, $log_message]);
        
        $pdo->commit();
        log_message('WARNING', "Payment expired for Order ID: $order_id | Session ID: $sessionId");
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_message('ERROR', "Error processing checkout.session.expired: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle successful payment intents
 */
function handlePaymentIntentSucceeded($pdo, $paymentIntent) {
    try {
        // Check if this is related to a Checkout Session
        // Payment Intents might come from both direct charges and Checkout
        // We'll handle it only if it's not already handled by checkout.session.completed
        
        $order_id = $paymentIntent->metadata->order_id ?? null;
        if (!$order_id) {
            log_message('WARNING', "No order_id in metadata for PaymentIntent: " . $paymentIntent->id);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Find payment with this order_id and status not 'completed'
        $stmt = $pdo->prepare("SELECT * FROM payment 
                              WHERE order_id = ? AND payment_status != 'completed'");
        $stmt->execute([$order_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            // Payment might have been already processed via checkout.session.completed
            log_message('INFO', "No pending payment found for order_id: $order_id - likely already processed");
            return;
        }
        
        $payment_id = $payment['payment_id'];
        
        // Update payment status
        $stmt = $pdo->prepare("UPDATE payment 
                              SET payment_status = 'completed', 
                                  stripe_created = ? 
                              WHERE payment_id = ?");
        $stmt->execute([$paymentIntent->created, $payment_id]);
        
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders 
                              SET delivery_status = 'packing' 
                              WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        // Log the payment success
        $stmt = $pdo->prepare("INSERT INTO payment_log 
                              (payment_id, log_level, log_message) 
                              VALUES (?, 'info', ?)");
        $log_message = "Payment intent succeeded: " . $paymentIntent->id;
        $stmt->execute([$payment_id, $log_message]);
        
        $pdo->commit();
        log_message('INFO', "Payment intent succeeded for Order ID: $order_id | Intent ID: {$paymentIntent->id}");
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_message('ERROR', "Error processing payment_intent.succeeded: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle failed payment intents
 */
function handlePaymentIntentFailed($pdo, $paymentIntent) {
    try {
        $order_id = $paymentIntent->metadata->order_id ?? null;
        if (!$order_id) {
            log_message('WARNING', "No order_id in metadata for PaymentIntent: " . $paymentIntent->id);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Find payment with this order_id
        $stmt = $pdo->prepare("SELECT * FROM payment 
                              WHERE order_id = ? AND payment_status != 'failed'");
        $stmt->execute([$order_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            log_message('INFO', "No active payment found for order_id: $order_id");
            return;
        }
        
        $payment_id = $payment['payment_id'];
        
        // Update payment status
        $stmt = $pdo->prepare("UPDATE payment 
                              SET payment_status = 'failed',
                                  stripe_created = ? 
                              WHERE payment_id = ?");
        $stmt->execute([$paymentIntent->created, $payment_id]);
        
        // Log the payment failure
        $stmt = $pdo->prepare("INSERT INTO payment_log 
                              (payment_id, log_level, log_message) 
                              VALUES (?, 'error', ?)");
        
        $error_message = $paymentIntent->last_payment_error ? 
                        $paymentIntent->last_payment_error->message : 
                        "Unknown error";
                        
        $log_message = "Payment intent failed: " . $paymentIntent->id . " - " . $error_message;
        $stmt->execute([$payment_id, $log_message]);
        
        $pdo->commit();
        log_message('ERROR', "Payment failed for Order ID: $order_id | Intent ID: {$paymentIntent->id} | Error: $error_message");
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_message('ERROR', "Error processing payment_intent.payment_failed: " . $e->getMessage());
        throw $e;
    }
}
?> 