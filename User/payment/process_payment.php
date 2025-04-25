<?php
declare(strict_types=1);
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/services/OrderService.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;
$csrf_token = generateCsrfToken();

// Ensure the order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: ../order/cart.php');
    exit;
}

$order_id = $_GET['order_id'];

// Debug log function for stock updates
function debug_log($message, $data = []) {
    $log_file = __DIR__ . '/logs/stock_update_debug.log';
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
    
    // Verify the order exists and belongs to this user
    $order = $db->fetchOne(
        "SELECT * FROM orders WHERE order_id = ? AND user_id = ?",
        [$order_id, $user_id]
    );
    
    if (!$order) {
        throw new Exception("Invalid order or order not found");
    }
    
    // Check if order already has a completed payment
    $completed_payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ? AND payment_status = 'completed'",
        [$order_id]
    );
    
    if ($completed_payment) {
        // Redirect to success page if payment is already completed
        header("Location: success.php?order_id=" . urlencode($order_id));
        exit;
    }
    
    // Get order items for display
    $orderItems = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.product_img1, p.brand 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?",
        [$order_id]
    );
    
    // Calculate totals
    $totalItems = 0;
    $totalPrice = $order['total_price'];
    
    foreach ($orderItems as $item) {
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
            // Create the charge
            $charge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'myr',
                'source' => $stripe_token,
                'description' => "Charge for Order #$order_id",
                'metadata' => [
                    'order_id' => $order_id,
                    'user_id' => $user_id
                ]
            ]);
            
            // Record payment in database
            $db->beginTransaction();
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
            
            // Update order status to "prepare" instead of "packing"
            $db->execute(
                "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
                [$order_id]
            );
            
            // Verify payment with Stripe before updating stock
            try {
                $orderService = new OrderService($db);
                
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
                    
                    error_log("Payment verification failed for order #$order_id with Stripe ID: " . $charge->id);
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
            
            $db->commit();
            
            // Redirect to success page
            header("Location: success.php?order_id=" . urlencode($order_id));
            exit;
            
        } catch (\Stripe\Exception\CardException $e) {
            // Card was declined
            $error = "Your card was declined: " . $e->getMessage();
            
            // Log the error
            error_log("Card declined: " . $e->getMessage());
            
            // Record failed payment
            logFailedPayment($db, $order_id, $totalPrice, $e->getMessage(), 'card_declined');
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Stripe API error
            $error = "Payment processing error: " . $e->getMessage();
            
            // Log the error
            error_log("Stripe API error: " . $e->getMessage());
            
            // Record failed payment
            logFailedPayment($db, $order_id, $totalPrice, $e->getMessage(), 'api_error');
            
        } catch (Exception $e) {
            // General error
            $error = "An error occurred during payment processing: " . $e->getMessage();
            
            // Log the error
            error_log("Payment error: " . $e->getMessage());
            
            // Record failed payment
            logFailedPayment($db, $order_id, $totalPrice, $e->getMessage(), 'general_error');
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Payment processing error: " . $e->getMessage());
    
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
}

/**
 * Log a failed payment attempt
 * 
 * @param Database $db Database connection
 * @param string $order_id Order ID
 * @param float $amount Payment amount
 * @param string $error_message Error message
 * @param string $error_code Error code
 * @return void
 */
function logFailedPayment(Database $db, string $order_id, float $amount, string $error_message, string $error_code = ''): void {
    try {
        $db->beginTransaction();
        $payment_id = uniqid('pay_', true) . bin2hex(random_bytes(8));
        
        // Insert failed payment record
        $db->execute(
            "INSERT INTO payment (payment_id, order_id, total_amount, payment_status, payment_method, currency, last_error) 
             VALUES (?, ?, ?, 'failed', 'stripe_card', 'MYR', ?)",
            [$payment_id, $order_id, $amount, $error_message]
        );
        
        // Log payment failure
        $db->execute(
            "INSERT INTO payment_log (payment_id, log_level, log_message) 
             VALUES (?, 'error', ?)",
            [$payment_id, "Payment failed: $error_code - $error_message"]
        );
        
        $db->commit();
    } catch (Exception $e) {
        if ($db->isTransactionActive()) {
            $db->rollback();
        }
        error_log("Failed to log payment failure: " . $e->getMessage());
    }
}

// Function to format price
function formatPrice($price) {
    return 'RM ' . number_format((float)$price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' https://js.stripe.com 'unsafe-inline'; connect-src 'self' https://*.stripe.com https://api.stripe.com; frame-src 'self' https://*.stripe.com https://*.hcaptcha.com; img-src 'self' data: https://*.stripe.com; worker-src blob: https://*.stripe.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;">
    <title>Payment - VeroSports</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .page-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
            padding: 40px 0;
        }
        
        .payment-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #222;
            text-align: center;
        }
        
        h2 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #222;
        }
        
        /* Error and Success Messages */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        /* Payment Layout */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .payment-form, .order-summary {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 25px;
        }
        
        .order-summary {
            height: fit-content;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group.half {
            flex: 1;
        }
        
        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #555;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        /* Credit Card Input Styling */
        .card-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .card-number-input {
            padding-right: 110px; /* Space for the card logos */
            letter-spacing: 1px;
        }
        
        .card-icons {
            position: absolute;
            right: 10px;
            display: flex;
            gap: 5px;
        }
        
        .card-logo {
            height: 24px;
            width: auto;
        }
        
        /* Stripe Elements Styling */
        .stripe-element-container {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            height: 45px;
            transition: border-color 0.3s;
        }
        
        .stripe-element-container.StripeElement--focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .stripe-element-container.StripeElement--invalid {
            border-color: #dc3545;
        }
        
        /* Expiry Date Styling */
        .expiry-container {
            display: flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }
        
        .expiry-select {
            flex: 1;
            padding-right: 25px;
        }
        
        .expiry-separator {
            font-size: 18px;
            color: #777;
        }
        
        .expiry-display {
            position: absolute;
            right: 0;
            width: 70px;
            text-align: center;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
        }
        
        /* Error text for validations */
        .error-text {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            min-height: 18px;
        }
        
        /* Button Styling */
        .button-container {
            margin-top: 25px;
        }
        
        .button-wrapper {
            position: relative;
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 12px 20px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        .btn-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        /* Spinner for loading state */
        .spinner {
            display: none;
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }
        
        /* Back Link */
        .return-link {
            display: block;
            text-align: center;
            color: #6c757d;
            text-decoration: none;
        }
        
        .return-link:hover {
            text-decoration: underline;
        }
        
        /* Security Info */
        .security-info {
            display: flex;
            align-items: center;
            margin-top: 20px;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        
        .security-info i {
            font-size: 18px;
            color: #28a745;
            margin-right: 10px;
        }
        
        .security-info p {
            font-size: 14px;
            color: #666;
        }
        
        /* Order Item Styling */
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .item-quantity {
            font-size: 14px;
            color: #777;
        }
        
        .item-price {
            font-weight: 500;
        }
        
        /* Summary Totals */
        .summary-totals {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row.total {
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .payment-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                order: -1;
            }
        }
        
        @media (max-width: 576px) {
            .payment-container {
                padding: 0 10px;
            }
            
            h1 {
                font-size: 24px;
                margin-bottom: 20px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            .payment-form, .order-summary {
                padding: 15px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
    <div class="page-container">
        
        <main>
            <div class="payment-container">
                <h1>Credit Card Payment</h1>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                        <p style="margin-top: 10px;">
                            <a href="payment_methods.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="return-link">Back to Payment Methods</a>
                        </p>
                    </div>
                <?php elseif ($success): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php else: ?>
                    <div class="payment-grid">
                        <div class="payment-form">
                            <h2>Enter Payment Details</h2>
                            
                            <form id="payment-form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                                
                                <div class="form-group">
                                    <label for="card-number">Bank card information</label>
                                    <div id="card-number-element" class="stripe-element-container"></div>
                                    <div id="card-number-error" class="error-text"></div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group half">
                                        <label for="card-expiry">Card expiration date</label>
                                        <div id="card-expiry-element" class="stripe-element-container"></div>
                                        <div id="card-expiry-error" class="error-text"></div>
                                    </div>
                                    
                                    <div class="form-group half">
                                        <label for="card-cvc">CVC/CVV</label>
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
                                        <button id="submit-button" type="submit" class="btn btn-primary">
                                            Pay <?php echo formatPrice($totalPrice); ?>
                                        </button>
                                        <div id="spinner" class="spinner"></div>
                                    </div>
                                    <a href="payment_methods.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="return-link">Back to Payment Methods</a>
                                </div>
                            </form>
                            
                            <div class="security-info">
                                <i class="fas fa-lock"></i>
                                <p>Your payment information is secure. We use SSL encryption to protect your data.</p>
                            </div>
                        </div>
                        
                        <div class="order-summary">
                            <h2>Order Summary</h2>
                            
                            <?php foreach ($orderItems as $item): ?>
                                <div class="order-item">
                                    <div class="item-details">
                                        <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="item-quantity">Size: <?php echo htmlspecialchars($item['product_size']); ?> | Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                                    </div>
                                    <p class="item-price">
                                        <?php 
                                        $itemPrice = $item['price'] * $item['quantity'];
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
                                    <span>Shipping</span>
                                    <span>Free</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total</span>
                                    <span><?php echo formatPrice($totalPrice); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        
    </div>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Add comment with development note about HTTPS requirement
        /**
         * Note: Stripe requires HTTPS in production environments.
         * This warning can be ignored in development, but ensure your 
         * production server uses HTTPS before going live.
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're using HTTPS
            if (window.location.protocol !== 'https:' && !window.location.hostname.includes('localhost')) {
                console.warn('Warning: Stripe requires HTTPS for production use. Current connection is not secure.');
            }

            // Initialize Stripe with fallback worker handling
            const stripeFallbackOptions = {
                apiVersion: '2023-10-16'
            };
            
            const stripe = Stripe('pk_test_51R3yBQQZPLk7FzRY3uO9YLeLKEbmLgOWzlD43uf0xHYeHdVC13kMzpCw5zhRPnp215QEwdZz7F9qmeMT6dv2ZmC600HNBheJIT', stripeFallbackOptions);
            const elements = stripe.elements();
            
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
            
            // Create individual Stripe Elements for each field
            const cardNumberElement = elements.create('cardNumber', {
                style: elementStyle,
                showIcon: true,
                iconStyle: 'solid'
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
            const spinner = document.getElementById('spinner');
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
                submitButton.textContent = 'Processing...';
                spinner.style.display = 'block';
                
                // Create a payment method using the card elements
                stripe.createPaymentMethod({
                    type: 'card',
                    card: cardNumberElement,
                    billing_details: {
                        name: cardName.value.trim()
                    }
                }).then(function(result) {
                    if (result.error) {
                        // Show error
                        cardErrors.textContent = result.error.message;
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay <?php echo formatPrice($totalPrice); ?>';
                        spinner.style.display = 'none';
                    } else {
                        // Now create a token for backward compatibility with the server
                        stripe.createToken(cardNumberElement, {
                            name: cardName.value.trim()
                        }).then(function(tokenResult) {
                            if (tokenResult.error) {
                                // Show error
                                cardErrors.textContent = tokenResult.error.message;
                                submitButton.disabled = false;
                                submitButton.textContent = 'Pay <?php echo formatPrice($totalPrice); ?>';
                                spinner.style.display = 'none';
                            } else {
                                // Send the token to the server
                                const hiddenInput = document.createElement('input');
                                hiddenInput.setAttribute('type', 'hidden');
                                hiddenInput.setAttribute('name', 'stripe_token');
                                hiddenInput.setAttribute('value', tokenResult.token.id);
                                form.appendChild(hiddenInput);
                                
                                // Set confirmation message
                                cardErrors.textContent = 'Processing your payment...';
                                cardErrors.style.color = '#28a745';
                                
                                // Submit the form
                                form.submit();
                            }
                        });
                    }
                }).catch(function(error) {
                    // Handle any other errors that might occur
                    cardErrors.textContent = 'An error occurred while processing your card. Please try again.';
                    console.error('Stripe payment processing error:', error);
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay <?php echo formatPrice($totalPrice); ?>';
                    spinner.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html> 