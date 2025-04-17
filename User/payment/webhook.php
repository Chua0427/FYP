<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

// Stripe API Key
\Stripe\Stripe::setApiKey($stripeSecretKey);
$endpoint_secret = 'whsec_C7rNUfMziKdqbIHkd4Sz2VnFysE5nwSx';

// Ensure logs directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

function log_message($level, $message) {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/webhook.log');
}

// Read the event payload from POST
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    // Verify webhook signature
try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch (\UnexpectedValueException $e) {
        log_message('ERROR', 'Invalid payload: ' . $e->getMessage());
        http_response_code(400);
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        log_message('ERROR', 'Invalid signature: ' . $e->getMessage());
        http_response_code(400);
        exit();
    }
    
    // Initialize database connection
    $db = new Database();
    
    // Log the event type
    log_message('INFO', 'Webhook received: ' . $event->type);
    
    // Process the event based on type
    switch ($event->type) {
        case 'payment_intent.succeeded':
            handlePaymentIntentSucceeded($db, $event->data->object);
            break;
            
        case 'payment_intent.payment_failed':
            handlePaymentIntentFailed($db, $event->data->object);
            break;
            
        case 'payment_intent.canceled':
            handlePaymentIntentCanceled($db, $event->data->object);
            break;
            
        case 'charge.refunded':
            handleChargeRefunded($db, $event->data->object);
            break;
            
        case 'checkout.session.completed':
            handleCheckoutSessionCompleted($db, $event->data->object);
            break;
            
        default:
            // For other events, just log them
            log_message('INFO', "Unhandled event type: {$event->type}");
    }

    // Return a 200 response to Stripe
    http_response_code(200);

} catch (Exception $e) {
    log_message('ERROR', "Webhook Error: " . $e->getMessage());
    http_response_code(500);
} finally {
    // Ensure database connection is closed if exists
    if (isset($db)) {
        $db->close();
    }
}

/**
 * Handle payment_intent.succeeded event
 * 
 * @param Database $db Database connection
 * @param \Stripe\PaymentIntent $paymentIntent The payment intent object
 */
function handlePaymentIntentSucceeded(Database $db, $paymentIntent) {
    try {
        // Get metadata from the payment intent
        $orderId = $paymentIntent->metadata->order_id ?? null;
        $userId = $paymentIntent->metadata->user_id ?? null;
        
        if (!$orderId) {
            log_message('WARNING', "Payment succeeded but no order_id in metadata: {$paymentIntent->id}");
            return;
        }
        
        // Begin transaction
        $db->beginTransaction();
        
        // Update payment record
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntent->id]
        );
        
        if ($payment) {
            // Update existing payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'completed', 
                     stripe_created = ? 
                 WHERE payment_id = ?",
                [$paymentIntent->created, $payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment completed via webhook | Stripe ID: {$paymentIntent->id}"]
            );
        } else {
            // Payment record not found - this could happen if webhook arrives before charge.php finishes
            log_message('WARNING', "Payment success webhook received but no payment record found for Stripe ID: {$paymentIntent->id}");
        }
        
        // Update order status
        $db->execute(
            "UPDATE orders 
             SET delivery_status = 'packing' 
             WHERE order_id = ?",
            [$orderId]
        );
        
        // Commit the transaction
        $db->commit();
        
        log_message('INFO', "Payment success processed: Order ID: {$orderId}, Stripe ID: {$paymentIntent->id}");
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process payment success: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle payment_intent.payment_failed event
 * 
 * @param Database $db Database connection
 * @param \Stripe\PaymentIntent $paymentIntent The payment intent object
 */
function handlePaymentIntentFailed(Database $db, $paymentIntent) {
    try {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        
        if (!$orderId) {
            log_message('WARNING', "Payment failed but no order_id in metadata: {$paymentIntent->id}");
            return;
        }
        
        // Begin transaction
        $db->beginTransaction();
        
        // Update payment record
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntent->id]
        );
        
        if ($payment) {
            // Get the error message if available
            $lastError = "";
            if (isset($paymentIntent->last_payment_error) && $paymentIntent->last_payment_error) {
                $lastError = $paymentIntent->last_payment_error->message ?? 'Unknown error';
            }
            
            // Update payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'failed', 
                     last_error = ? 
                 WHERE payment_id = ?",
                [$lastError, $payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'error', ?)",
                [$payment['payment_id'], "Payment failed via webhook | Error: {$lastError}"]
            );
        } else {
            log_message('WARNING', "Payment failed webhook received but no payment record found for Stripe ID: {$paymentIntent->id}");
        }
        
        // Commit the transaction
        $db->commit();
        
        log_message('INFO', "Payment failure processed: Order ID: {$orderId}, Stripe ID: {$paymentIntent->id}");
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process payment failure: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle payment_intent.canceled event
 * 
 * @param Database $db Database connection
 * @param \Stripe\PaymentIntent $paymentIntent The payment intent object
 */
function handlePaymentIntentCanceled(Database $db, $paymentIntent) {
    try {
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntent->id]
        );
        
        if ($payment) {
            // Begin transaction
            $db->beginTransaction();
            
            // Update payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'failed', 
                     last_error = 'Payment canceled' 
                 WHERE payment_id = ?",
                [$payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment canceled via webhook"]
            );
            
            // Commit the transaction
            $db->commit();
            
            log_message('INFO', "Payment cancellation processed: Payment ID: {$payment['payment_id']}, Stripe ID: {$paymentIntent->id}");
        } else {
            log_message('WARNING', "Payment canceled webhook received but no payment record found for Stripe ID: {$paymentIntent->id}");
        }
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process payment cancellation: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle charge.refunded event
 * 
 * @param Database $db Database connection
 * @param \Stripe\Charge $charge The charge object
 */
function handleChargeRefunded(Database $db, $charge) {
    try {
        // Find the payment by charge ID or payment intent ID
        $paymentIntentId = $charge->payment_intent;
        
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntentId]
        );
        
        if ($payment) {
            // Begin transaction
            $db->beginTransaction();
            
            // Update payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'refunded' 
                 WHERE payment_id = ?",
                [$payment['payment_id']]
            );
            
            // Add log entry
            $refundAmount = $charge->amount_refunded / 100; // Convert to decimal
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment refunded via webhook | Amount: {$refundAmount} {$charge->currency}"]
            );
            
            // Commit the transaction
            $db->commit();
            
            log_message('INFO', "Refund processed: Payment ID: {$payment['payment_id']}, Stripe ID: {$paymentIntentId}");
        } else {
            log_message('WARNING', "Refund webhook received but no payment record found for Payment Intent: {$paymentIntentId}");
        }
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process refund: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle checkout.session.completed event
 * 
 * @param Database $db Database connection
 * @param \Stripe\Checkout\Session $session The checkout session object
 */
function handleCheckoutSessionCompleted(Database $db, $session) {
    try {
        // This handles Stripe Checkout integration if you're using it
        $paymentIntentId = $session->payment_intent;
        
        if (!$paymentIntentId) {
            log_message('WARNING', "Checkout session completed but no payment_intent found: {$session->id}");
            return;
        }
        
        // Find the payment by payment intent ID
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntentId]
        );
        
        if ($payment) {
            // Begin transaction
            $db->beginTransaction();
            
            // Update payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'completed' 
                 WHERE payment_id = ?",
                [$payment['payment_id']]
            );
            
            // Update order status
            $db->execute(
                "UPDATE orders 
                 SET delivery_status = 'packing' 
                 WHERE order_id = ?",
                [$payment['order_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Checkout session completed | Session ID: {$session->id}"]
            );
            
            // Commit the transaction
            $db->commit();
            
            log_message('INFO', "Checkout session completed: Payment ID: {$payment['payment_id']}, Session ID: {$session->id}");
        } else {
            log_message('WARNING', "Checkout session completed but no payment record found for Payment Intent: {$paymentIntentId}");
        }
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process checkout session completed: " . $e->getMessage());
        throw $e;
    }
}
