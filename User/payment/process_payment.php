<?php
declare(strict_types=1);

// Enhanced debugging for troubleshooting redirect issues
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/process_payment_errors.log');

// Create logs directory if needed
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Log access to this script
file_put_contents($logDir . '/process_payment_access.log', 
    date('[Y-m-d H:i:s]') . " Process payment accessed. REQUEST_METHOD: " . 
    ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN') . PHP_EOL, FILE_APPEND);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/services/OrderService.php';

// Initialize session if not already started
ensure_session_started();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Ensure we have shipping address from checkout
if (!isset($_SESSION['checkout_shipping_address']) || empty($_SESSION['checkout_shipping_address'])) {
    header('Location: checkout.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$shipping_address = $_SESSION['checkout_shipping_address'];
$error = null;
$success = null;
$csrf_token = generateCsrfToken();

// Debug log function for stock updates and payment flow
function debug_log($message, $data = []) {
    $log_file = __DIR__ . '/logs/payment_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
    
    if (!empty($data)) {
        $log_message .= " - " . json_encode($data);
    }
    
    $log_message .= PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $log_dir = dirname($log_file);
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

try {
    // Initialize Database
    $db = new Database();
    
    // Check if we have cart items in session
    if (isset($_SESSION['checkout_cart_items'])) {
        $cartItems = $_SESSION['checkout_cart_items'];
        // Sort cart items by brand then newest first to match checkout page
        usort($cartItems, function($a, $b) {
            $brandCmp = strcmp($a['brand'] ?? '', $b['brand'] ?? '');
            if ($brandCmp !== 0) {
                return $brandCmp;
            }
            return strcmp($b['added_at'] ?? '', $a['added_at'] ?? '');
        });
    } else {
        // Fetch cart items directly if not in session
        $cartItems = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand, \n             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price\n             FROM cart c \n             JOIN product p ON c.product_id = p.product_id \n             WHERE c.user_id = ?\n             ORDER BY p.brand ASC, c.added_at DESC",
            [$user_id]
        );
        
        if (empty($cartItems)) {
            throw new Exception("Your cart is empty. Please add items before proceeding to payment.");
        }
        
        // Store cart items in session
        $_SESSION['checkout_cart_items'] = $cartItems;
    }
    
    // Get total price from session or calculate
    if (isset($_SESSION['checkout_total_price'])) {
        $totalPrice = $_SESSION['checkout_total_price'];
    } else {
        // Calculate total price
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['final_price'] * $item['quantity'];
        }
        $_SESSION['checkout_total_price'] = $totalPrice;
    }
    
    // Calculate total items
    $totalItems = 0;
    foreach ($cartItems as $item) {
        $totalItems += $item['quantity'];
    }
    
    // Process stripe payment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stripe_token']) && isset($_POST['csrf_token'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Invalid form submission. Please try again.");
        }
        
        $stripe_token = $_POST['stripe_token'];
        
        // Initialize Stripe
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        
        // Prepare payment amount (convert to cents)
        $amount = (int) round($totalPrice * 100);
        
        try {
            debug_log("Starting Stripe payment processing", [
                'user_id' => $user_id, 
                'amount' => $amount, 
                'currency' => 'myr'
            ]);

            // Create the charge
            $charge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'myr',
                'source' => $stripe_token,
                'description' => "Charge for VeroSports purchase",
                'metadata' => [
                    'user_id' => $user_id,
                    'items_count' => $totalItems
                ]
            ]);

            debug_log("Stripe charge created successfully", [
                'stripe_id' => $charge->id, 
                'status' => $charge->status
            ]);
            
            // Start transaction for order creation
            $db->beginTransaction();
            
            try {
                // Create order using OrderService
                $orderService = new OrderService($db, $GLOBALS['logger']);
                
                // Get selected cart items from session
                $selectedCartItems = isset($_SESSION['selected_cart_items']) ? $_SESSION['selected_cart_items'] : null;
                
                // Create order from cart items
                $orderResult = $orderService->createOrderFromCart(
                    $user_id,
                    $shipping_address,
                    true, // Check stock
                    [
                        'payment_method' => 'stripe_card',
                        'stripe_id' => $charge->id,
                        'payment_status' => 'completed'
                    ],
                    $selectedCartItems, // Selected cart items from session
                    true  // Clear cart after order creation
                );
                
                // Get order ID from result
                $order_id = $orderResult['order_id'];

                debug_log("Order created successfully after payment", [
                    'order_id' => $order_id, 
                    'total_price' => $totalPrice
                ]);
                
                // Generate payment ID
                $payment_id = uniqid('pay_', true) . bin2hex(random_bytes(8));
                
                // Insert payment record
                $db->execute(
                    "INSERT INTO payment (payment_id, order_id, total_amount, payment_status, payment_method, stripe_id, currency) 
                     VALUES (?, ?, ?, 'completed', 'stripe_card', ?, 'MYR')",
                    [$payment_id, $order_id, $totalPrice, $charge->id]
                );
                
                // Log payment success
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) 
                     VALUES (?, 'info', ?)",
                    [$payment_id, "Payment completed successfully via card payment. Stripe ID: " . $charge->id]
                );
                
                // Update order status to "prepare" 
                $db->execute(
                    "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
                    [$order_id]
                );
                
                // Verify payment with Stripe before updating stock
                try {
                    debug_log("Starting stock update process for order #{$order_id}", [
                        'payment_method' => 'stripe_card',
                        'stripe_id' => $charge->id
                    ]);
                    
                    // Verify payment with Stripe API
                    $verified = $orderService->verifyStripePayment($charge->id, $payment_id, $stripeSecretKey);
                    
                    debug_log("Payment verification result", [
                        'order_id' => $order_id,
                        'verified' => $verified,
                        'stripe_id' => $charge->id
                    ]);
                    
                    if ($verified) {
                        // Update stock after verified payment confirmation
                        try {
                            $result = $orderService->updateStockAfterPayment((int)$order_id);
                            debug_log("Stock update completed", [
                                'order_id' => $order_id,
                                'result' => $result
                            ]);
                            
                            // Log successful stock update
                            $db->execute(
                                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                                 VALUES (?, 'info', ?)",
                                [$payment_id, "Stock updated successfully after payment verification"]
                            );
                        } catch (Exception $stockUpdateError) {
                            debug_log("Stock update failed with exception", [
                                'order_id' => $order_id,
                                'error' => $stockUpdateError->getMessage(),
                                'trace' => $stockUpdateError->getTraceAsString()
                            ]);
                            throw $stockUpdateError;
                        }
                    } else {
                        // Log verification failure
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) 
                             VALUES (?, 'warning', ?)",
                            [$payment_id, "Payment verification failed with Stripe - stock not updated"]
                        );
                        
                        error_log("Payment verification failed with Stripe ID: " . $charge->id);
                    }
                } catch (Exception $verifyError) {
                    debug_log("Stripe verification error in process_payment.php", [
                        'order_id' => $order_id,
                        'error' => $verifyError->getMessage()
                    ]);
                    
                    // Try direct stock update as fallback
                    try {
                        $result = $orderService->updateStockAfterPayment((int)$order_id);
                        debug_log("Fallback stock update after verification error in process_payment.php", [
                            'order_id' => $order_id,
                            'result' => $result
                        ]);
                        
                        // Log fallback update
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                            [$payment_id, "Stock updated via fallback after Stripe API error in process_payment"]
                        );
                    } catch (Exception $stockUpdateError) {
                        // Log but continue - don't fail the payment entirely
                        debug_log("Fallback stock update failed in process_payment.php", [
                            'order_id' => $order_id,
                            'error' => $stockUpdateError->getMessage()
                        ]);
                        $db->execute(
                            "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'error', ?)",
                            [$payment_id, "Stock update failed after fallback attempt: " . $stockUpdateError->getMessage()]
                        );
                    }
                }

                // Clear session checkout data
                unset($_SESSION['checkout_shipping_address']);
                unset($_SESSION['checkout_total_price']);
                unset($_SESSION['checkout_cart_items']);
                unset($_SESSION['checkout_payment_method']);
                unset($_SESSION['selected_cart_items']);  // Clear selected cart items
                
                // Commit the transaction
                $db->commit();

                // Send invoice email immediately after payment
                require_once __DIR__ . '/../app/services/InvoiceService.php';
                try {
                    $invoiceService = new \App\Services\InvoiceService($db);
                    $invoiceService->generateAndSendInvoice((int)$order_id);
                } catch (Exception $e) {
                    debug_log("Invoice send error in process_payment", ['order_id' => $order_id, 'error' => $e->getMessage()]);
                }

                // Prevent any unexpected output before redirect
                ob_start();
                
                // Clear any pending output buffers to prevent header issues
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // Log to regular error_log for debugging
                error_log("Payment complete - preparing redirect to success page, order_id={$order_id}");
                
                // Determine redirect URL
                $redirectUrl = "/FYP/FYP/User/payment/success.php?order_id=" . urlencode((string)$order_id);

                // Attempt PHP header redirect
                if (!headers_sent($file, $line)) {
                    debug_log("Redirecting to success page via header", ['order_id' => $order_id, 'location' => $redirectUrl]);
                    header("Location: {$redirectUrl}");
                    exit;
                }

                // Fallback when headers already sent
                debug_log("Headers already sent at $file:$line, using JavaScript redirect", ['order_id' => $order_id, 'location' => $redirectUrl]);
                echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>Redirecting...</title>";
                echo "<script>window.location.href = " . json_encode($redirectUrl) . ";</script>";
                echo "</head><body><p>Redirecting to success page... If you are not redirected, <a href=\"" . htmlspecialchars(
                    $redirectUrl, ENT_QUOTES, 'UTF-8') . "\">click here</a>.</p></body></html>";
                exit;
            } catch (Exception $orderError) {
                // Roll back if order creation failed
                if ($db->isTransactionActive()) {
                    $db->rollback();
                }
                
                debug_log("Order creation failed after payment", [
                    'error' => $orderError->getMessage()
                ]);
                
                // Try to refund the charge
                try {
                    \Stripe\Refund::create([
                        'charge' => $charge->id,
                    ]);
                    
                    debug_log("Payment refunded due to order creation failure", [
                        'stripe_id' => $charge->id
                    ]);
                    
                    $error = "Payment was processed but order creation failed. A refund has been issued.";
                } catch (Exception $refundError) {
                    debug_log("Failed to refund charge after order creation error", [
                        'stripe_id' => $charge->id,
                        'refund_error' => $refundError->getMessage()
                    ]);
                    
                    $error = "Payment was processed but order creation failed. Please contact customer support.";
                }
            }
            
        } catch (\Stripe\Exception\CardException $e) {
            // Card was declined
            $error = "Your card was declined: " . $e->getMessage();
            
            // Log the error
            error_log("Card declined: " . $e->getMessage());
            debug_log("Card declined", ['error' => $e->getMessage()]);
            
            // No order to log failure against yet
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Stripe API error
            $error = "Payment processing error: " . $e->getMessage();
            
            // Log the error
            error_log("Stripe API error: " . $e->getMessage());
            debug_log("Stripe API error", ['error' => $e->getMessage()]);
            
            // No order to log failure against yet
            
        } catch (Exception $e) {
            // General error
            $error = "An error occurred during payment processing: " . $e->getMessage();
            
            // Log the error
            error_log("Payment error: " . $e->getMessage());
            debug_log("General payment error", ['error' => $e->getMessage()]);
            
            // No order to log failure against yet
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Payment processing error: " . $e->getMessage());
    debug_log("Payment processing exception", ['error' => $e->getMessage()]);
    
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
}

/**
 * Log a failed payment attempt to local database
 * 
 * @param Database $db Database connection
 * @param float $amount Payment amount
 * @param string $error_message Error message
 * @param string $error_code Error code
 * @return string|null Generated payment_id if successful, null otherwise
 */
function logPaymentFailure(Database $db, float $amount, string $error_message, string $error_code = ''): ?string {
    try {
        $db->beginTransaction();
        $payment_id = uniqid('pay_', true) . bin2hex(random_bytes(8));
        
        // Insert failed payment record (without order_id)
        $db->execute(
            "INSERT INTO payment (payment_id, total_amount, payment_status, payment_method, currency, last_error) 
             VALUES (?, ?, 'failed', 'stripe_card', 'MYR', ?)",
            [$payment_id, $amount, $error_message]
        );
        
        // Log payment failure
        $db->execute(
            "INSERT INTO payment_log (payment_id, log_level, log_message) 
             VALUES (?, 'error', ?)",
            [$payment_id, "Payment failed: $error_code - $error_message"]
        );
        
        $db->commit();
        return $payment_id;
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        error_log("Failed to log payment failure: " . $e->getMessage());
        return null;
    }
}

// Function to format price
function formatPrice($price) {
    return 'MYR ' . number_format((float)$price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
      content="
        default-src 'self';
        script-src 'self' https://js.stripe.com 'unsafe-inline';
        connect-src 'self' https://*.stripe.com https://api.stripe.com;
        frame-src 'self' https://*.stripe.com;
        img-src 'self' data: https://*.stripe.com;
        worker-src blob: https://*.stripe.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src 'self' https://fonts.gstatic.com;
      ">
    <title>Payment - VeroSports</title>
    <link rel="stylesheet" href="process_payment.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-container">
        
        <main>
            <div class="payment-container">
                <h1>Payment Details</h1>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                        <p style="margin-top: 10px;">
                            <a href="payment_methods.php" class="return-link">Go back to payment methods</a>
                        </p>
                    </div>
                <?php else: ?>
                    <form id="payment-form" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="stripe_token" id="stripe_token">
                        
                        <div class="payment-grid">
                            <div class="payment-form">
                                <h2>Card Details</h2>
                                
                                <div style="display: none;">
                                    <div id="link-authentication-element"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="card-number-element">Bank card information</label>
                                    <div id="card-number-element" class="stripe-element-container"></div>
                                    <div id="card-number-error" class="error-text"></div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group half">
                                        <label for="card-expiry-element">Card expiration date</label>
                                        <div id="card-expiry-element" class="stripe-element-container"></div>
                                        <div id="card-expiry-error" class="error-text"></div>
                                    </div>
                                    
                                    <div class="form-group half">
                                        <label for="card-cvc-element">CVC/CVV</label>
                                        <div id="card-cvc-element" class="stripe-element-container"></div>
                                        <div id="card-cvc-error" class="error-text"></div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="card-name">Cardholder name</label>
                                    <input type="text" id="card-name" class="card-name-input" placeholder="Full name" autocomplete="cc-name">
                                    <div id="card-name-error" class="error-text"></div>
                                </div>
                                
                                <div id="card-errors" role="alert" class="error-text"></div>
                                
                                <div class="button-container">
                                    <div class="button-wrapper">
                                        <button id="submit-button" class="btn btn-primary">Pay <?php echo formatPrice($totalPrice); ?></button>
                                        <div id="payment-processing">
                                            <div class="spinner"></div>
                                            <span>Processing...</span>
                                        </div>
                                    </div>
                                    <a href="payment_methods.php" class="return-link">Change payment method</a>
                                </div>
                                
                                <div class="security-info">
                                    <i class="fas fa-lock"></i>
                                    <p>Your payment information is secure. We use SSL encryption to protect your data.</p>
                                </div>
                            </div>
                            
                            <div class="order-summary">
                                <h2>Order Summary</h2>
                                
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                            <p class="item-quantity">Size: <?php echo htmlspecialchars($item['product_size']); ?> | Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                                        </div>
                                        <p class="item-price">
                                            <?php 
                                            $itemPrice = $item['final_price'] * $item['quantity'];
                                            echo formatPrice($itemPrice); 
                                            ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="summary-totals">
                                    <div class="summary-row">
                                        <span>Subtotal (<?php echo $totalItems; ?> items)</span>
                                        <span><?php echo formatPrice($totalPrice); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Sales Tax (6% SST)</span>
                                        <span>Included</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Shipping</span>
                                        <span>Free</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span>Total</span>
                                        <span><?php echo formatPrice($totalPrice); ?></span>
                                    </div>
                                    <div class="billing-note">
                                        <p>* All prices are in Malaysian Ringgit (MYR)</p>
                                        <p>* 6% Sales and Service Tax (SST) is included in all listed prices</p>
                                        <p>* Free standard shipping on all domestic orders</p>
                                        <p>* Your card will be charged: <?php echo formatPrice($totalPrice); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize Stripe with fallback worker handling
        const stripeFallbackOptions = {
            apiVersion: '2023-10-16'
        };
        
        // Initialize Stripe
        const stripe = Stripe('pk_test_51R3yBQQZPLk7FzRY3uO9YLeLKEbmLgOWzlD43uf0xHYeHdVC13kMzpCw5zhRPnp215QEwdZz7F9qmeMT6dv2ZmC600HNBheJIT', stripeFallbackOptions);
        
        // Initialize elements with basic configuration
        const elements = stripe.elements({
            mode: 'payment',
            currency: 'myr',
            amount: <?php echo (int)round($totalPrice * 100); ?>,
            appearance: {
                theme: 'stripe',
            }
        });
        
        // Style for Stripe Elements
        const elementStyle = {
            base: {
                fontFamily: '"Inter", sans-serif',
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#dc3545',
                iconColor: '#dc3545'
            }
        };
        
        // Create and mount Link Authentication Element with email prefill
        const linkAuthElement = elements.create('linkAuthentication', {
            defaultValues: {
                email: '<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>',
            }
        });
        linkAuthElement.mount('#link-authentication-element');
        
        // Create individual Stripe Elements for each field with Link enabled
        const cardNumberElement = elements.create('cardNumber', {
            style: elementStyle,
            showIcon: true,
            // Allow Link integration
        });
        
        const cardExpiryElement = elements.create('cardExpiry', {
            style: elementStyle
        });
        
        const cardCvcElement = elements.create('cardCvc', {
            style: elementStyle
        });
        
        // Mount the elements
        cardNumberElement.mount('#card-number-element');
        cardExpiryElement.mount('#card-expiry-element');
        cardCvcElement.mount('#card-cvc-element');
        
        // DOM Elements
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const processingOverlay = document.getElementById('payment-processing');
        const cardName = document.getElementById('card-name');
        
        // Error elements
        const cardNumberError = document.getElementById('card-number-error');
        const cardExpiryError = document.getElementById('card-expiry-error');
        const cardCvcError = document.getElementById('card-cvc-error');
        const cardNameError = document.getElementById('card-name-error');
        const cardErrors = document.getElementById('card-errors');
        
        // Add error handling for each element
        cardNumberElement.addEventListener('change', function(event) {
            if (event.error) {
                cardNumberError.textContent = event.error.message;
            } else {
                cardNumberError.textContent = '';
            }
        });
        
        cardExpiryElement.addEventListener('change', function(event) {
            if (event.error) {
                cardExpiryError.textContent = event.error.message;
            } else {
                cardExpiryError.textContent = '';
            }
        });
        
        cardCvcElement.addEventListener('change', function(event) {
            if (event.error) {
                cardCvcError.textContent = event.error.message;
            } else {
                cardCvcError.textContent = '';
            }
        });
        
        // Validate card form before submission
        function validateCardForm() {
            let isValid = true;
            
            // Reset error messages
            cardNameError.textContent = '';
            cardErrors.textContent = '';
            
            // Validate cardholder name
            if (!cardName.value.trim()) {
                cardNameError.textContent = 'Cardholder name is required';
                isValid = false;
            }
            
            return isValid;
        }
        
        // Handle form submission
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validate form first
            if (!validateCardForm()) {
                return;
            }
            
            // Disable the submit button to prevent multiple submissions
            submitButton.disabled = true;
            processingOverlay.style.display = 'flex';
            
            // Create a payment method instead of just a token
            stripe.createPaymentMethod({
                type: 'card',
                card: cardNumberElement,
                billing_details: {
                    name: cardName.value.trim(),
                    email: '<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>'
                }
            }).then(function(result) {
                if (result.error) {
                    // Show error in the form
                    cardErrors.textContent = result.error.message;
                    
                    // Re-enable submit button and hide loading
                    submitButton.disabled = false;
                    processingOverlay.style.display = 'none';
                } else {
                    // For backward compatibility with your existing code,
                    // still create a token too
                    createToken();
                }
            });
            
            // Create a token for backward compatibility
            function createToken() {
                stripe.createToken(cardNumberElement, {
                    name: cardName.value.trim()
                }).then(function(result) {
                    if (result.error) {
                        // Show error in the form
                        cardErrors.textContent = result.error.message;
                        
                        // Re-enable submit button and hide loading
                        submitButton.disabled = false;
                        processingOverlay.style.display = 'none';
                    } else {
                        // Send the token to the server
                        document.getElementById('stripe_token').value = result.token.id;
                        form.submit();
                    }
                }).catch(function(error) {
                    console.error('Error:', error);
                    
                    // Show generic error
                    cardErrors.textContent = 'An unexpected error occurred. Please try again.';
                    
                    // Re-enable submit button and hide loading
                    submitButton.disabled = false;
                    processingOverlay.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html> 