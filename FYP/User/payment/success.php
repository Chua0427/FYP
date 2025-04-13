<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

// Initialize Database
$db = new Database();

try {
    // Get session_id and order_id from URL parameters
    $session_id = $_GET['session_id'] ?? '';
    $order_id = $_GET['order_id'] ?? '';

    if (empty($order_id)) {
        throw new Exception('Missing required parameters');
    }

    // Get order details
    $order = $db->fetchOne("SELECT * FROM orders WHERE order_id = ?", [$order_id]);
    if (!$order) {
        throw new Exception('Order not found');
    }

    // Initialize Stripe
    \Stripe\Stripe::setApiKey($stripeSecretKey);

    // If session_id is provided, verify the session
    if (!empty($session_id)) {
        // Retrieve the checkout session
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        // Verify the session belongs to this order
        if ((string)$session->metadata->order_id !== (string)$order_id) {
            throw new Exception('Invalid session');
        }

        // Get payment details by session ID
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE stripe_id = ?",
            [$session_id]
        );
    } else {
        // Get payment details by order ID
        $payment = $db->fetchOne(
            "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1",
            [$order_id]
        );
    }

    if (!$payment) {
        throw new Exception('Payment record not found');
    }

    // Update payment status if needed
    if ($payment['payment_status'] !== 'completed') {
        $db->execute(
            "UPDATE payment SET payment_status = 'completed' WHERE payment_id = ?",
            [$payment['payment_id']]
        );
        
        // Update order status to packing
        $db->execute(
            "UPDATE orders SET delivery_status = 'packing' WHERE order_id = ?",
            [$order_id]
        );
        
        // Add log entry
        $db->execute(
            "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
            [$payment['payment_id'], "Payment marked as completed via success page"]
        );
    }

    // Get order items
    $items = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.product_img1 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?",
        [$order_id]
    );

} catch (Exception $e) {
    // Log error and redirect to error page
    error_log("Payment success error: " . $e->getMessage());
    header("Location: ../index.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .success-icon {
            color: #28a745;
            font-size: 4rem;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .product-image {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h1 class="mt-3">Payment Successful!</h1>
            <p class="lead">Thank you for your purchase. Your order has been confirmed.</p>
        </div>

        <div class="order-details">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['order_at'])); ?></p>
            <p><strong>Total Amount:</strong> MYR <?php echo number_format($order['total_price'], 2); ?></p>

            <h4 class="mt-4">Items Purchased</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['product_img1'])): ?>
                                    <img src="<?php echo htmlspecialchars('/FYP' . $item['product_img1']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="product-image">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['product_size']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>MYR <?php echo number_format($item['price'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
            <a href="../orders.php" class="btn btn-secondary ms-2">View Orders</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 