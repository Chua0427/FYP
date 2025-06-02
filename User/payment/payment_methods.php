<?php
declare(strict_types=1);
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

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
            // Store payment method in session
            $_SESSION['checkout_payment_method'] = $payment_method;
            
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
                                    <div class="payment-option selected">
                                        <input type="radio" id="payment-card" name="payment_method" value="card" checked>
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
                                    <button type="submit" class="btn btn-primary">Process Payment</button>
                                    <a href="checkout.php" class="return-link">Back to Order Details</a>
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
            
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                });
            });
        });
    </script>
</body>
</html> 