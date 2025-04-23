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
            
            // Update order status
            $db->execute(
                "UPDATE orders SET delivery_status = 'packing' WHERE order_id = ?",
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
    <title>Payment - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset and base styles */
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
        
        /* Messages */
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
        
        .payment-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 25px;
        }
        
        .order-summary {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 25px;
            height: fit-content;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #555;
        }
        
        #card-element {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        #card-element.StripeElement--focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        #card-errors {
            color: #dc3545;
            font-size: 14px;
            margin-top: 8px;
        }
        
        /* Buttons */
        .button-container {
            margin-top: 25px;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            border: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
            width: 100%;
            padding: 12px;
        }
        
        .btn-primary:hover:not(:disabled) {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        .btn-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        /* Return Link */
        .return-link {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #6c757d;
            text-decoration: none;
        }
        
        .return-link:hover {
            text-decoration: underline;
        }
        
        /* Order items */
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
        
        /* Summary totals */
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
        
        /* Loading spinner */
        .spinner {
            display: none;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }
        
        .button-wrapper {
            position: relative;
        }
        
        /* Card icons */
        .card-icons {
            display: flex;
            margin-bottom: 15px;
        }
        
        .card-icon {
            font-size: 28px;
            margin-right: 10px;
            color: #555;
        }
        
        /* Security info */
        .security-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
        }
        
        .security-info i {
            font-size: 18px;
            margin-right: 10px;
            color: #28a745;
        }
        
        /* Responsive */
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
        }
    </style>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
    <div class="page-container">
        <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
        
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
                            
                            <div class="card-icons">
                                <span class="card-icon"><i class="fab fa-cc-visa"></i></span>
                                <span class="card-icon"><i class="fab fa-cc-mastercard"></i></span>
                                <span class="card-icon"><i class="fab fa-cc-amex"></i></span>
                                <span class="card-icon"><i class="fab fa-cc-discover"></i></span>
                            </div>
                            
                            <form id="payment-form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                                
                                <div class="form-group">
                                    <label for="card-element">Credit or Debit Card</label>
                                    <div id="card-element">
                                        <!-- Stripe Element will be inserted here -->
                                    </div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                
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
        
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    </div>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Stripe
            const stripe = Stripe('pk_test_51R3yBQQZPLk7FzRY3uO9YLeLKEbmLgOWzlD43uf0xHYeHdVC13kMzpCw5zhRPnp215QEwdZz7F9qmeMT6dv2ZmC600HNBheJIT');
            const elements = stripe.elements();
            
            // Create card element
            const style = {
                base: {
                    fontFamily: '"Inter", sans-serif',
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4'
                    },
                    lineHeight: '1.6'
                },
                invalid: {
                    color: '#dc3545',
                    iconColor: '#dc3545'
                }
            };
            
            const card = elements.create('card', { style });
            
            // Mount the card element
            card.mount('#card-element');
            
            // Handle validation errors
            card.addEventListener('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            
            // Handle form submission
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('spinner');
            
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Disable the submit button to prevent multiple submissions
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
                spinner.style.display = 'block';
                
                // Create a token
                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        // Show error and re-enable submit button
                        const errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay <?php echo formatPrice($totalPrice); ?>';
                        spinner.style.display = 'none';
                    } else {
                        // Send the token to the server
                        const hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripe_token');
                        hiddenInput.setAttribute('value', result.token.id);
                        form.appendChild(hiddenInput);
                        
                        // Submit the form
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html> 