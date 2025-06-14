<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/services/OrderService.php';

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

// Enhanced logging for debugging
log_message('DEBUG', "Webhook request received - Headers: " . json_encode(getallheaders()));
log_message('DEBUG', "Webhook signature header: " . $sig_header);

try {
    // Verify webhook signature
    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch (\UnexpectedValueException $e) {
        log_message('ERROR', 'Invalid payload: ' . $e->getMessage());
        log_message('DEBUG', 'Payload received: ' . substr($payload, 0, 500) . '...(truncated)');
        http_response_code(400);
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        log_message('ERROR', 'Invalid signature: ' . $e->getMessage());
        log_message('DEBUG', 'Endpoint secret used: ' . substr($endpoint_secret, 0, 5) . '...(truncated)');
        log_message('DEBUG', 'Signature header received: ' . $sig_header);
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
            
        case 'charge.succeeded':
            handleChargeSucceeded($db, $event->data->object);
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
    // Access the global Stripe secret key
    global $stripeSecretKey;
    
    try {
        // Get metadata from the payment intent
        $orderId = $paymentIntent->metadata->order_id ?? null;
        $userId = $paymentIntent->metadata->user_id ?? null;
        
        // Begin transaction
        $db->beginTransaction();
        
        // Check if we have an order_id in metadata
        if (!$orderId) {
            // This might be a payment created from cart directly with no order yet
            // Check if we have a payment record
            $payment = $db->fetchOne(
                "SELECT * FROM payment WHERE stripe_id = ?", 
                [$paymentIntent->id]
            );
            
            if ($payment && $payment['order_id']) {
                // We have a payment with order ID, use it
                $orderId = $payment['order_id'];
            } else if ($userId) {
                // No order_id exists, we need to create one from cart
                log_message('INFO', "Payment succeeded with user_id but no order_id, creating order from cart for user: $userId");
                
                require_once __DIR__ . '/../app/services/OrderService.php';
                $orderService = new OrderService($db);
                
                // We should get the shipping address from session or user profile
                // For webhook, we'll use the user's default address
                $userInfo = $db->fetchOne("SELECT * FROM users WHERE user_id = ?", [$userId]);
                
                if (!$userInfo) {
                    log_message('ERROR', "User not found for webhook order creation: $userId");
                    throw new \Exception("User not found: $userId");
                }
                
                $shippingAddress = $userInfo['address'] . ', ' . $userInfo['city'] . ', ' . 
                                  $userInfo['postcode'] . ', ' . $userInfo['state'];
                
                // Create order from cart
                $result = $orderService->createOrderFromCart(
                    $userId,
                    $shippingAddress,
                    true, // Check stock
                    [
                        'payment_method' => 'stripe_card',
                        'stripe_id' => $paymentIntent->id,
                        'payment_status' => 'completed'
                    ],
                    null, // All cart items
                    true  // Clear cart
                );
                
                $orderId = $result['order_id'];
                
                log_message('INFO', "Order created from cart in webhook: $orderId");
            } else {
                log_message('WARNING', "Payment succeeded but no order_id or user_id in metadata: {$paymentIntent->id}");
                $db->rollback();
                return;
            }
        }
        
        // Update payment record if it exists
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$paymentIntent->id]
        );
        
        // Generate a payment ID if needed
        $payment_id = $payment['payment_id'] ?? uniqid('pay_', true) . bin2hex(random_bytes(8));
        
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
            // Create new payment record
            // We need to get the amount
            $amount = $paymentIntent->amount / 100; // Convert from cents to base currency
            
            $db->execute(
                "INSERT INTO payment (payment_id, order_id, total_amount, payment_status, payment_method, stripe_id, currency) 
                 VALUES (?, ?, ?, 'completed', 'stripe_card', ?, 'MYR')",
                [$payment_id, $orderId, $amount, $paymentIntent->id]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment_id, "Payment record created via webhook | Stripe ID: {$paymentIntent->id}"]
            );
        }
        
        // Update order status
        $db->execute(
            "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
            [$orderId]
        );
        
        // Update stock levels after confirmed payment
        try {
            $orderService = new OrderService($db);
            
            // Double-check with Stripe API if payment is really successful
            if ($payment) {
                try {
                    // Directly pass the API key to ensure it's available
                    $verified = $orderService->verifyStripePayment($paymentIntent->id, $payment['payment_id'], $stripeSecretKey);
                    
                    if ($verified) {
                        // Update stock after payment verification
                        $orderService->updateStockAfterPayment((int)$orderId);
                        
                        // Log successful verification and stock update
                        log_message('INFO', "Payment verified with Stripe and stock updated for order ID: {$orderId}");
                        
                        // Add detailed log entry
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'info', ?)",
                            [$payment['payment_id'], "Stock updated after Stripe verification via webhook"]
                        );
                    } else {
                        // Log verification failure
                        log_message('WARNING', "Payment verification failed with Stripe API for order ID: {$orderId}");
                        
                        // Add detailed log entry
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'warning', ?)",
                            [$payment['payment_id'], "Payment verification failed with Stripe - stock not updated"]
                        );
                    }
                } catch (Exception $apiError) {
                    // Handle API verification errors separately
                    log_message('ERROR', "Stripe API verification error: " . $apiError->getMessage());
                    
                    // Try direct stock update as fallback
                    $orderService->updateStockAfterPayment((int)$orderId);
                    log_message('INFO', "Fallback stock update after Stripe API error for order ID: {$orderId}");
                    
                    // Add detailed log entry
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) 
                         VALUES (?, 'info', ?)",
                        [$payment['payment_id'], "Stock updated via fallback after Stripe API error in webhook"]
                    );
                    
                    // Generate and send invoice PDF as a fallback
                    try {
                        require_once __DIR__ . '/../app/services/InvoiceService.php';
                        $invoiceService = new \App\Services\InvoiceService($db);
                        $invoiceSent = $invoiceService->generateAndSendInvoice((int)$orderId);
                        
                        if ($invoiceSent) {
                            $db->execute(
                                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                                 VALUES (?, 'info', ?)",
                                [$payment['payment_id'], "Invoice PDF generated and sent to customer via webhook (fallback)"]
                            );
                        }
                    } catch (\Exception $invoiceError) {
                        log_message('ERROR', "Invoice generation error (fallback) for order #{$orderId}: " . $invoiceError->getMessage());
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'error', ?)",
                            [$payment['payment_id'], "Invoice error (fallback): " . $invoiceError->getMessage()]
                        );
                    }
                }
            } else {
                // No payment record - just try to update stock directly
                $orderService->updateStockAfterPayment((int)$orderId);
                log_message('INFO', "Stock updated directly for order ID: {$orderId} (no payment record)");
            }
        } catch (Exception $e) {
            log_message('ERROR', "Stock update error: " . $e->getMessage());
            
            if ($payment) {
                // Add error log
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'error', ?)",
                    [$payment['payment_id'], "Stock update error: " . $e->getMessage()]
                );
            }
        }
        
        // Generate and send invoice PDF after successful payment
        if ($payment) {
            try {
                // Include the InvoiceService class
                require_once __DIR__ . '/../app/services/InvoiceService.php';
                
                // Create an instance of the InvoiceService
                $invoiceService = new \App\Services\InvoiceService($db);
                
                // Generate and send the invoice
                $invoiceSent = $invoiceService->generateAndSendInvoice((int)$orderId);
                
                // Log the result
                if ($invoiceSent) {
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) 
                         VALUES (?, 'info', ?)",
                        [$payment['payment_id'], "Invoice PDF generated and sent to customer via webhook"]
                    );
                    log_message('INFO', "Invoice generated and emailed for order ID: {$orderId}");
                } else {
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) 
                         VALUES (?, 'warning', ?)",
                        [$payment['payment_id'], "Failed to generate and send invoice PDF via webhook"]
                    );
                    log_message('WARNING', "Failed to generate and email invoice for order ID: {$orderId}");
                }
            } catch (\Exception $invoiceError) {
                // Log invoice error but don't interrupt the flow
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'error', ?)",
                    [$payment['payment_id'], "Invoice error via webhook: " . $invoiceError->getMessage()]
                );
                log_message('ERROR', "Invoice generation error for order #{$orderId}: " . $invoiceError->getMessage());
            }
        }
        
        // Commit all changes
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
        // Get metadata from the session
        $orderId = $session->metadata->order_id ?? null;
        $userId = $session->metadata->user_id ?? null;
        
        if (!$orderId) {
            log_message('WARNING', "Checkout session completed but no order_id in metadata: {$session->id}");
            return;
        }
        
        // Begin transaction
        $db->beginTransaction();
        
        // Update payment record - first try with session ID
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$session->id]
        );
        
        if (!$payment && $session->payment_intent) {
            // If not found by session ID, try with payment intent ID
            $payment = $db->fetchOne(
                "SELECT * FROM payment WHERE stripe_id = ?", 
                [$session->payment_intent]
            );
        }
        
        if ($payment) {
            // Update existing payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'completed', 
                     stripe_id = ? 
                 WHERE payment_id = ?",
                [$session->id, $payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment completed via checkout session webhook | Session ID: {$session->id}"]
            );
        } else {
            // Payment record not found - this could happen if webhook arrives before checkout completes
            log_message('WARNING', "Checkout session webhook received but no payment record found: {$session->id}");
        }
        
        // Update order status
        $db->execute(
            "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
            [$orderId]
        );
        
        // Update stock levels after confirmed payment
        try {
            $orderService = new OrderService($db);
            $orderService->updateStockAfterPayment((int)$orderId);
            
            log_message('INFO', "Stock updated for checkout session: {$session->id}, Order ID: {$orderId}");
            
            if ($payment) {
                // Add detailed log entry
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'info', ?)",
                    [$payment['payment_id'], "Stock updated via checkout session webhook"]
                );
            }
            
            // Generate and send invoice PDF after successful payment
            if ($payment) {
                try {
                    // Include the InvoiceService class
                    require_once __DIR__ . '/../app/services/InvoiceService.php';
                    
                    // Create an instance of the InvoiceService
                    $invoiceService = new \App\Services\InvoiceService($db);
                    
                    // Generate and send the invoice
                    $invoiceSent = $invoiceService->generateAndSendInvoice((int)$orderId);
                    
                    // Log the result
                    if ($invoiceSent) {
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'info', ?)",
                            [$payment['payment_id'], "Invoice PDF generated and sent via checkout session webhook"]
                        );
                        log_message('INFO', "Invoice generated and emailed for checkout session: {$session->id}, Order ID: {$orderId}");
                    } else {
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'warning', ?)",
                            [$payment['payment_id'], "Failed to generate and send invoice PDF via checkout session webhook"]
                        );
                        log_message('WARNING', "Failed to generate invoice for checkout session: {$session->id}, Order ID: {$orderId}");
                    }
                } catch (\Exception $invoiceError) {
                    // Log invoice error but don't interrupt the flow
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) 
                         VALUES (?, 'error', ?)",
                        [$payment['payment_id'], "Invoice error via checkout session webhook: " . $invoiceError->getMessage()]
                    );
                    log_message('ERROR', "Invoice generation error for checkout session: {$session->id}, Order ID: {$orderId}: " . $invoiceError->getMessage());
                }
            }
        } catch (Exception $e) {
            log_message('ERROR', "Stock update error for checkout session: " . $e->getMessage());
            
            if ($payment) {
                // Add error log
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'error', ?)",
                    [$payment['payment_id'], "Stock update error via checkout session: " . $e->getMessage()]
                );
            }
        }
        
        // Commit the transaction
        $db->commit();
        
        log_message('INFO', "Checkout session completed: Payment ID: {$payment['payment_id']}, Session ID: {$session->id}");
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process checkout session completed: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Handle charge.succeeded event
 * 
 * @param Database $db Database connection
 * @param \Stripe\Charge $charge The charge object
 */
function handleChargeSucceeded(Database $db, $charge) {
    // Access the global Stripe secret key
    global $stripeSecretKey;
    
    try {
        // Get metadata from the charge
        $orderId = $charge->metadata->order_id ?? null;
        $userId = $charge->metadata->user_id ?? null;
        
        if (!$orderId) {
            log_message('WARNING', "Charge succeeded but no order_id in metadata: {$charge->id}");
            return;
        }
        
        // Begin transaction
        $db->beginTransaction();
        
        // Update payment record
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?", 
            [$charge->id]
        );
        
        if ($payment) {
            // Update existing payment record
            $db->execute(
                "UPDATE payment 
                 SET payment_status = 'completed', 
                     stripe_created = ? 
                 WHERE payment_id = ?",
                [$charge->created, $payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment completed via webhook (charge.succeeded) | Stripe ID: {$charge->id}"]
            );
        } else {
            // Payment record not found - this could happen if webhook arrives before charge.php finishes
            log_message('WARNING', "Charge.succeeded webhook received but no payment record found for Stripe ID: {$charge->id}");
        }
        
        // Update order status
        $db->execute(
            "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
            [$orderId]
        );
        
        // Update stock levels after confirmed payment
        try {
            $orderService = new OrderService($db);
            
            // Double-check with Stripe API if payment is really successful
            if ($payment) {
                try {
                    // Directly pass the API key to ensure it's available
                    $verified = $orderService->verifyStripePayment($charge->id, $payment['payment_id'], $stripeSecretKey);
                    
                    if ($verified) {
                        // Update stock after payment verification
                        $orderService->updateStockAfterPayment((int)$orderId);
                        
                        // Log successful verification and stock update
                        log_message('INFO', "Payment verified with Stripe and stock updated for order ID: {$orderId} via charge.succeeded");
                        
                        // Add detailed log entry
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'info', ?)",
                            [$payment['payment_id'], "Stock updated after Stripe verification via webhook (charge.succeeded)"]
                        );
                    } else {
                        // Log verification failure
                        log_message('WARNING', "Payment verification failed with Stripe API for order ID: {$orderId} (charge.succeeded)");
                        
                        // Add detailed log entry
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'warning', ?)",
                            [$payment['payment_id'], "Payment verification failed with Stripe - stock not updated (charge.succeeded)"]
                        );
                    }
                } catch (Exception $apiError) {
                    // Handle API verification errors separately
                    log_message('ERROR', "Stripe API verification error: " . $apiError->getMessage());
                    
                    // Try direct stock update as fallback
                    $orderService->updateStockAfterPayment((int)$orderId);
                    log_message('INFO', "Fallback stock update after Stripe API error for order ID: {$orderId}");
                    
                    // Add detailed log entry
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) 
                         VALUES (?, 'info', ?)",
                        [$payment['payment_id'], "Stock updated via fallback after Stripe API error (charge.succeeded)"]
                    );
                }
            } else {
                // No payment record, can't verify with API, but webhook should be trusted
                // This is a fallback case
                $orderService->updateStockAfterPayment((int)$orderId);
                log_message('INFO', "Stock updated via webhook without verification for order ID: {$orderId} (charge.succeeded)");
            }
        } catch (Exception $stockError) {
            // Log stock update error, but don't fail the payment processing
            log_message('ERROR', "Failed to verify/update stock for order: {$orderId} | Error: {$stockError->getMessage()} (charge.succeeded)");
            
            // Add a more detailed log entry if payment record exists
            if ($payment) {
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'error', ?)",
                    [$payment['payment_id'], "Stock update/verification failed (charge.succeeded): {$stockError->getMessage()}"]
                );
            }
        }
        
        // Commit the transaction
        $db->commit();
        
        log_message('INFO', "Charge.succeeded event processed: Order ID: {$orderId}, Stripe ID: {$charge->id}");
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        log_message('ERROR', "Failed to process charge.succeeded: " . $e->getMessage());
        throw $e;
    }
}
