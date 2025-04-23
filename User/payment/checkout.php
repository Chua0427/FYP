<?php
declare(strict_types=1);
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;
$order_id = null;
$csrf_token = generateCsrfToken();

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
    
    // Check if order_id is provided (for returning to an existing order)
    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];
        
        // Verify the order exists and belongs to this user
        $order = $db->fetchOne(
            "SELECT * FROM orders WHERE order_id = ? AND user_id = ?",
            [$order_id, $user_id]
        );
        
        if (!$order) {
            throw new Exception("Invalid order or order not found");
        }
    } else {
        // Check if cart is empty
        $cartItems = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
             FROM cart c 
             JOIN product p ON c.product_id = p.product_id 
             WHERE c.user_id = ?",
            [$user_id]
        );
        
        if (empty($cartItems)) {
            throw new Exception("Your cart is empty. Please add items before proceeding to checkout.");
        }
    }
    
    // Get user details for shipping
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE user_id = ?",
        [$user_id]
    );
    
    if (!$user) {
        throw new Exception("User information not found");
    }
    
    // Process form submission - Create Order
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order']) && isset($_POST['csrf_token'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Invalid form submission. Please try again.");
        }

        // Validate shipping address
        $shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';
        if (empty($shipping_address)) {
            throw new Exception("Shipping address is required");
        }

        // Handle order creation
        if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
            if (empty($cartItems)) {
                throw new Exception("Your cart is empty. Please add items before proceeding to checkout.");
            }
            $db->beginTransaction();
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['final_price'] * $item['quantity'];
            }
            $db->execute("INSERT INTO orders (user_id, total_price, shipping_address) VALUES (?, ?, ?)", [$user_id, $totalPrice, $shipping_address]);
            $order_id = $db->getInsertId();
            foreach ($cartItems as $item) {
                $db->execute(
                    "INSERT INTO order_items (order_id, product_id, product_size, quantity, price) VALUES (?, ?, ?, ?, ?)",
                    [$order_id, $item['product_id'], $item['product_size'], $item['quantity'], $item['final_price']]
                );
            }
            // Clear cart after creating the order
            $db->execute("DELETE FROM cart WHERE user_id = ?", [$user_id]);
            $db->commit();

            // Redirect to payment selection page
            header("Location: payment_methods.php?order_id=" . urlencode((string)$order_id));
            exit;
        } else {
            // Order exists, redirect to payment method selection
            header("Location: payment_methods.php?order_id=" . urlencode($_POST['order_id']));
            exit;
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    // Log error
    error_log("Checkout error: " . $e->getMessage());
}

// Function to format price
function formatPrice($price) {
    return 'RM ' . number_format((float)$price, 2);
}

// Prepare cart or order data for display
if (isset($order_id)) {
    // Get order details if we have an order ID
    $orderItems = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.product_img1, p.brand 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?",
        [$order_id]
    );
    
    $items = $orderItems;
    $totalItems = 0;
    $totalPrice = 0;
    
    foreach ($items as $item) {
        $totalItems += $item['quantity'];
        $totalPrice += $item['price'] * $item['quantity'];
    }
} else {
    // Use cart items since we don't have an order yet
    $items = $cartItems;
    $totalItems = 0;
    $totalPrice = 0;
    
    foreach ($items as $item) {
        $totalItems += $item['quantity'];
        $totalPrice += $item['final_price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
        
        .checkout-container {
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
        
        /* Checkout Layout */
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .checkout-details {
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
        
        input, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .help-text {
            font-size: 13px;
            color: #888;
            margin-top: 6px;
        }
        
        /* Sections */
        .shipping-section, .payment-section {
            margin-bottom: 30px;
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
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
            width: 100%;
            padding: 12px;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border: 1px solid #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        /* Cart Link */
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
        
        /* Responsive */
        @media (max-width: 992px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                order: -1;
            }
        }
        
        @media (max-width: 576px) {
            .checkout-container {
                padding: 0 10px;
            }
            
            h1 {
                font-size: 24px;
                margin-bottom: 20px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            .checkout-details, .order-summary {
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
            <div class="checkout-container">
                <h1>Checkout</h1>
                
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
                    <form method="post" id="checkout-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <?php if (isset($order_id)): ?>
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars((string)$order_id); ?>">
                        <?php endif; ?>
                        
                        <div class="checkout-grid">
                            <div class="checkout-details">
                                <section class="shipping-section">
                                    <h2>Shipping Information</h2>
                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="shipping_address">Shipping Address</label>
                                        <textarea id="shipping_address" name="shipping_address" required><?php echo htmlspecialchars($user['address'] . ', ' . $user['city'] . ', ' . $user['postcode'] . ', ' . $user['state']); ?></textarea>
                                        <p class="help-text">You can edit your shipping address if needed</p>
                                    </div>
                                </section>
                                
                                <section class="payment-section">
                                    <h2>Next Step: Payment</h2>
                                    <p>After confirming your order, you will be directed to select your payment method.</p>
                                </section>
                                
                                <div class="button-container">
                                    <button type="submit" name="create_order" class="btn btn-primary">Continue to Payment</button>
                                    <a href="../order/cart.php" class="return-link">Return to Cart</a>
                                </div>
                            </div>
                            
                            <div class="order-summary">
                                <h2>Order Summary</h2>
                                
                                <?php foreach ($items as $item): ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="item-quantity">Size: <?php echo htmlspecialchars($item['product_size']); ?> | Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                                        </div>
                                        <p class="item-price">
                                            <?php 
                                            $itemPrice = isset($item['final_price']) ? 
                                                $item['final_price'] * $item['quantity'] : 
                                                $item['price'] * $item['quantity'];
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
                    </form>
                <?php endif; ?>
            </div>
        </main>
        
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    </div>
</body>
</html> 