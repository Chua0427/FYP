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
$csrf_token = generateCsrfToken();

// Ensure the order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: ../order/cart.php');
    exit;
}

$order_id = $_GET['order_id'];

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
    
    // Process form submission - payment method selection
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method']) && isset($_POST['csrf_token'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Invalid form submission. Please try again.");
        }

        $payment_method = $_POST['payment_method'];
        
        // Now that user has selected a payment method, clear the cart items related to this order
        require_once __DIR__ . '/../app/services/OrderService.php';
        $orderService = new OrderService($db, $GLOBALS['logger']);
        
        // Get the order items to identify which products to remove from cart
        $orderItems = $db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$order_id]
        );
        
        // Start transaction for cart removal
        $db->beginTransaction();
        
        try {
            // For each order item, remove matching cart item
            foreach ($orderItems as $orderItem) {
                $db->execute(
                    "DELETE FROM cart 
                     WHERE user_id = ? 
                     AND product_id = ? 
                     AND product_size = ?",
                    [$user_id, $orderItem['product_id'], $orderItem['product_size']]
                );
            }
            
            // Commit transaction
            $db->commit();
            
            // Log cart clearing
            $GLOBALS['logger']->info('Cart items cleared after payment method selection', [
                'user_id' => $user_id,
                'order_id' => $order_id
            ]);
        } catch (Exception $e) {
            // Rollback on error
            $db->rollback();
            // Log error but don't stop the process
            $GLOBALS['logger']->error('Failed to clear cart items: ' . $e->getMessage(), [
                'user_id' => $user_id,
                'order_id' => $order_id
            ]);
        }
        
        // Currently only supporting credit card payment
        if ($payment_method === 'card') {
            // Redirect to the payment processing page
            header("Location: process_payment.php?order_id=" . urlencode($order_id));
            exit;
        } else {
            throw new Exception("Selected payment method is not supported");
        }
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
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Payment method selection error: " . $e->getMessage());
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
    <title>Select Payment Method - VeroSports</title>
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
        
        .payment-methods {
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
        
        /* Payment Method Options */
        .payment-options {
            margin-top: 20px;
        }
        
        .payment-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            position: relative;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .payment-option:hover {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .payment-option.selected {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .payment-option-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .payment-icon {
            font-size: 24px;
            margin-right: 15px;
            color: #555;
        }
        
        .payment-option.selected .payment-icon {
            color: #007bff;
        }
        
        .payment-details {
            flex: 1;
        }
        
        .payment-title {
            display: block;
            font-weight: 500;
            font-size: 16px;
            margin-bottom: 4px;
        }
        
        .payment-description {
            font-size: 14px;
            color: #777;
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
        
        /* Card icons */
        .card-icons {
            display: flex;
            margin-top: 10px;
        }
        
        .card-icon {
            font-size: 24px;
            margin-right: 10px;
            color: #555;
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
            
            .payment-methods, .order-summary {
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
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                        
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
                                    <a href="checkout.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="return-link">Back to Order Details</a>
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