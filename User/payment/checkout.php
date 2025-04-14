<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

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
    
    // Process order creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate shipping address
        $shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';
        
        if (empty($shipping_address)) {
            throw new Exception("Shipping address is required");
        }
        
        if (!isset($order_id)) {
            // Create new order - Call the order_management API
            $formData = http_build_query([
                'shipping_address' => $shipping_address
            ]);
            
            // Use internal API call to order_management.php
            $url = "http://{$_SERVER['HTTP_HOST']}/FYP/User/payment/order_management.php?action=create_order";
            
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => $formData,
                    'ignore_errors' => true
                ]
            ];
            
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            // Check for errors
            if ($result === FALSE) {
                throw new Exception("Error creating order");
            }
            
            $response = json_decode($result, true);
            
            if (isset($response['success']) && $response['success']) {
                $order_id = $response['order_id'];
                $success = "Order created successfully! Proceeding to payment...";
                
                // Redirect to payment page
                header("Location: checkout_session.php?order_id=$order_id");
                exit;
            } else {
                throw new Exception($response['error'] ?? "Error creating order");
            }
        } else {
            // Existing order - proceed to payment
            header("Location: checkout_session.php?order_id=$order_id");
            exit;
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Function to format price
function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
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
    <link rel="stylesheet" href="checkout.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once '../Header_and_Footer/header.html'; ?>
    
    <main>
        <div class="checkout-container">
            <h1>Checkout</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                    <p><a href="../order/cart.php">Return to Cart</a></p>
                </div>
            <?php elseif ($success): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php else: ?>
                <form method="post" id="checkout-form">
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
                                <h2>Payment Method</h2>
                                <p>You will select your payment method in the next step.</p>
                            </section>
                        </div>
                        
                        <div class="order-summary">
                            <h2>Order Summary</h2>
                            
                            <?php
                            $totalItems = 0;
                            $totalPrice = 0;
                            
                            if (isset($order_id)) {
                                // Get order details if we have an order ID
                                $orderItems = $db->fetchAll(
                                    "SELECT oi.*, p.product_name, p.product_img1 
                                     FROM order_items oi 
                                     JOIN product p ON oi.product_id = p.product_id 
                                     WHERE oi.order_id = ?",
                                    [$order_id]
                                );
                                
                                foreach ($orderItems as $item) {
                                    $totalItems += $item['quantity'];
                                    $totalPrice += $item['price'] * $item['quantity'];
                                }
                                
                                // Display order items
                                foreach ($orderItems as $item) {
                                    echo '<div class="order-item">';
                                    echo '<div class="item-details">';
                                    echo '<p class="item-name">' . htmlspecialchars($item['product_name']) . ' (Size: ' . htmlspecialchars($item['product_size']) . ')</p>';
                                    echo '<p class="item-quantity">Qty: ' . htmlspecialchars($item['quantity']) . '</p>';
                                    echo '</div>';
                                    echo '<p class="item-price">' . formatPrice($item['price'] * $item['quantity']) . '</p>';
                                    echo '</div>';
                                }
                                
                            } else {
                                // Use cart items since we don't have an order yet
                                foreach ($cartItems as $item) {
                                    $totalItems += $item['quantity'];
                                    $totalPrice += $item['final_price'] * $item['quantity'];
                                
                                    echo '<div class="order-item">';
                                    echo '<div class="item-details">';
                                    echo '<p class="item-name">' . htmlspecialchars($item['product_name']) . ' (Size: ' . htmlspecialchars($item['product_size']) . ')</p>';
                                    echo '<p class="item-quantity">Qty: ' . htmlspecialchars($item['quantity']) . '</p>';
                                    echo '</div>';
                                    echo '<p class="item-price">' . formatPrice($item['final_price'] * $item['quantity']) . '</p>';
                                    echo '</div>';
                                }
                            }
                            ?>
                            
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
                            
                            <button type="submit" class="checkout-button">Proceed to Payment</button>
                            <a href="../order/cart.php" class="back-to-cart">Back to Cart</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once '../Header_and_Footer/footer.html'; ?>
</body>
</html> 