<?php
declare(strict_types=1);
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

// Add debug logging
if (function_exists('debug_session')) {
    debug_session('Payment Methods Page Load', __DIR__ . '/logs/payment_methods_debug.log');
}

// Ensure session is started safely
ensure_session_started();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;
$csrf_token = generateCsrfToken();

// Check for payment method error from process_payment.php
if (isset($_SESSION['payment_method_error'])) {
    $error = $_SESSION['payment_method_error'];
    unset($_SESSION['payment_method_error']);
}

// Clear any previous checkout cart data to stay in sync
unset($_SESSION['checkout_cart_items'], $_SESSION['checkout_total_price']);

// Get shipping address from session
$shipping_address = $_SESSION['checkout_shipping_address'];

try {
    // Initialize Database
    $db = new Database();
    
    // Check if we have selected items in session
    if (!isset($_SESSION['selected_cart_items'])) {
        throw new Exception("No items selected for checkout. Please return to your cart and select items.");
    }
    
    $selectedItems = $_SESSION['selected_cart_items'];
    if (empty($selectedItems)) {
        throw new Exception("No items selected for checkout. Please return to your cart and select items.");
    }
    
    // Check if we have cart items in session
    if (isset($_SESSION['checkout_cart_items'])) {
        $cartItems = $_SESSION['checkout_cart_items'];
        // Sort cart items by brand then newest first to match checkout page
        usort($cartItems, function($a, $b) {
            $brandCmp = strcmp($a['brand'] ?? '', $b['brand'] ?? '');
            if ($brandCmp !== 0) {
                return $brandCmp;
            }
            // Compare added_at descending
            return strcmp($b['added_at'] ?? '', $a['added_at'] ?? '');
        });
    } else {
        // Fetch cart items directly if not in session - only selected items
        $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
        $params = array_merge([$user_id], $selectedItems);
        
        $cartItems = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
             FROM cart c
             JOIN product p ON c.product_id = p.product_id
             WHERE c.user_id = ? AND c.cart_id IN ($placeholders)
             ORDER BY p.brand ASC, c.added_at DESC",
            $params
        );
        
        if (empty($cartItems)) {
            throw new Exception("Your selected items are no longer available. Please return to your cart.");
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
    
    // Calculate total original price and discount
    $totalOriginalPrice = 0;
    $totalDiscount = 0;
    foreach ($cartItems as $item) {
        $originalItemPrice = $item['price'] * $item['quantity'];
        $totalOriginalPrice += $originalItemPrice;
        
        if (isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']) {
            $totalDiscount += $originalItemPrice - ($item['discount_price'] * $item['quantity']);
        }
    }
    
    // Calculate total items
    $totalItems = 0;
    foreach ($cartItems as $item) {
        $totalItems += $item['quantity'];
    }
    
    // Process form submission - payment method selection
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method']) && isset($_POST['csrf_token'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Invalid form submission. Please try again.");
        }

        $payment_method = $_POST['payment_method'];
        
        // Currently only supporting credit card payment
        if ($payment_method === 'card') {
            // Debug session before setting payment flow vars
            if (function_exists('debug_session')) {
                debug_session('Before Payment Method Selected', __DIR__ . '/logs/payment_flow_debug.log');
            }
            
            // Store payment method in session
            $_SESSION['checkout_payment_method'] = $payment_method;
            
            // Generate a unique token for this payment flow and store in session
            $_SESSION['payment_flow_token'] = bin2hex(random_bytes(16));
            $_SESSION['payment_selection_timestamp'] = time();
            
            // Set payment flow stage to enforce the correct sequence
            $_SESSION['payment_flow_stage'] = 'method_selected';
            
            // Store the user's IP for additional validation
            $_SESSION['payment_flow_ip'] = $_SERVER['REMOTE_ADDR'];
            
            // Set flag to indicate this is a legitimate flow
            $_SESSION['payment_initiated'] = true;
            
            // Ensure all session variables are written
            session_write_close();
            
            // Debug: Store a copy of session data in a separate cookie to verify
            $sessionData = json_encode([
                'flow_stage' => 'method_selected',
                'time' => time(),
                'token_hash' => md5($_SESSION['payment_flow_token'] ?? '')
            ]);
            setcookie('payment_debug', $sessionData, 0, '/');
            
            // Log the payment flow transition
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->info('Payment method selected', [
                    'user_id' => $user_id,
                    'payment_method' => $payment_method,
                    'token' => substr($_SESSION['payment_flow_token'], 0, 8) . '...',
                    'flow_stage' => 'method_selected'
                ]);
            }
            
            // Redirect to the payment processing page
            header("Location: process_payment.php");
            exit;
        } else {
            throw new Exception("Selected payment method is not supported");
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Payment method selection error: " . $e->getMessage());
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
    <title>Select Payment Method - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="payment_methods.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
    <div class="page-container">
        <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
        
        <main>
            <div class="payment-container">
                <h1>Select Payment Method</h1>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                        <p style="margin-top: 10px;">
                            <a href="../order/cart.php" class="return-link">Return to Cart</a>
                        </p>
                    </div>
                <?php elseif ($success): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php else: ?>
                    <form method="post" id="payment-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        
                        <div class="payment-grid">
                            <div class="payment-methods">
                                <h2>Choose Payment Method</h2>
                                
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <input type="radio" id="payment-card" name="payment_method" value="card">
                                        <label for="payment-card" class="payment-option-label">
                                            <span class="payment-icon"><i class="fas fa-credit-card"></i></span>
                                            <div class="payment-details">
                                                <span class="payment-title">Credit/Debit Card</span>
                                                <span class="payment-description">Pay securely using your credit or debit card.</span>
                                                <div class="card-icons">
                                                    <span class="card-icon"><i class="fab fa-cc-visa"></i></span>
                                                    <span class="card-icon"><i class="fab fa-cc-mastercard"></i></span>
                                                    <span class="card-icon"><i class="fab fa-cc-amex"></i></span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="button-container">
                                    <button type="submit" id="submit-btn" class="btn btn-primary">Process Payment</button>
                                    <a href="checkout.php" class="return-link">Back to Order Details</a>
                                </div>
                            </div>
                            
                            <div class="order-summary">
                                <h2>Order Summary</h2>
                                
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="order-item-image">
                                        </div>
                                        <div class="item-details">
                                            <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                            <p class="item-quantity">Size: <?php echo htmlspecialchars($item['product_size']); ?> | Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                                        </div>
                                        <div class="item-price">
                                                    <?php
                                                    $itemPrice = $item['final_price'] * $item['quantity'];
                                                    echo formatPrice($itemPrice);
                                                    ?>
                                                    <?php if (isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']): ?>
                                                        <div class="original-price" style="text-decoration: line-through;">
                                                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="summary-totals">
                                    <div class="summary-row">
                                        <span>Subtotal (<?php echo $totalItems; ?> items)</span>
                                        <span><?php echo formatPrice($totalPrice); ?></span>
                                    </div>
                                    <?php if ($totalDiscount > 0): ?>
                                    <div class="summary-row discount">
                                        <span>Total Discount</span>
                                        <span id="checkout-discount">-<?php echo formatPrice($totalDiscount); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="summary-row">
                                        <span>Sales Tax (6% SST)</span>
                                        <span>Included</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span>Total</span>
                                        <span><?php echo formatPrice($totalPrice); ?></span>
                                    </div>
                                    <div class="billing-note">
                                        <p>* All prices are in Malaysian Ringgit (MYR)</p>
                                        <p>* 6% Sales and Service Tax (SST) is included in all listed prices</p>
                                        <p>* Free standard shipping on all domestic orders</p>
                                        <p>* Payment processed securely via Stripe</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
        
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle payment option selection
            const paymentOptions = document.querySelectorAll('.payment-option');
            const form = document.getElementById('payment-form');
            const submitBtn = document.getElementById('submit-btn');
            
            // Only add error container if form exists (prevents null reference on redirects)
            let errorMsgContainer;
            if (form) {
                // Error message container
                errorMsgContainer = document.createElement('div');
                errorMsgContainer.className = 'error-message payment-error';
                errorMsgContainer.style.display = 'none';
                errorMsgContainer.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Please select a payment method to continue.';
                form.insertBefore(errorMsgContainer, form.firstChild);
            }
            
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Hide error message if it was displayed and exists
                    if (errorMsgContainer) {
                        errorMsgContainer.style.display = 'none';
                    }
                });
            });
            
            // Form validation (only if form exists)
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check if any payment method is selected
                    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
                    
                    if (!selectedPayment) {
                        e.preventDefault();
                        errorMsgContainer.style.display = 'block';
                        errorMsgContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    
                    // Store selected payment method in session storage for validation on next page
                    sessionStorage.setItem('payment_method_selected', selectedPayment.value);
                    return true;
                });
            }
        });
    </script>
</body>
</html> 