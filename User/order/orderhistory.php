<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../payment/db.php';

// Check if user is authenticated
Auth::requireAuth();

// Set up logger for this page
$logger = $GLOBALS['logger'];

// Get user ID from session
$user_id = (int)$_SESSION['user_id'];

try {
    // Initialize database connection
    $db = new Database();
    
    // Fetch all user orders with payment information
    $orders = $db->fetchAll(
        "SELECT o.*, 
         (SELECT payment_status FROM payment WHERE order_id = o.order_id ORDER BY payment_at DESC LIMIT 1) as payment_status
         FROM orders o 
         WHERE o.user_id = ? AND o.delivery_status = 'delivered' 
         ORDER BY o.order_at DESC",
        [$user_id]
    );

    // For each order, fetch order items and check if they've been reviewed
    foreach ($orders as &$order) {
        // Get order items with product details
        $order['items'] = $db->fetchAll(
            "SELECT oi.*, p.product_name, p.product_img1,
             (SELECT COUNT(*) FROM review WHERE user_id = ? AND product_id = oi.product_id) as reviewed
             FROM order_items oi
             JOIN product p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?",
            [$user_id, $order['order_id']]
        );
        
        // Count items needing review
        $order['needs_review_count'] = 0;
        foreach ($order['items'] as $item) {
            if ($item['reviewed'] == 0) {
                $order['needs_review_count']++;
            }
        }
    }
    unset($order);
    
    // Count total orders
    $total_orders = count($orders);
    
    // Count delivered orders (which is now the same as total orders)
    $delivered_orders = $total_orders;
    
} catch (Exception $e) {
    $logger->error('Error fetching order history', [
        'user_id' => $user_id,
        'error' => $e->getMessage()
    ]);
    
    $error_message = "An error occurred while fetching your order history. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .order-history-container {
            max-width: 1200px;
            position: relative;
            top: 100px;
            margin: auto auto 150px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }
        
        .stat-box {
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0077cc;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f5f7fa;
            border-bottom: 1px solid #eee;
        }
        
        .order-id {
            font-weight: bold;
        }
        
        .order-date {
            color: #666;
        }
        
        .order-total {
            font-weight: bold;
            color: #333;
        }
        
        .order-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-prepare {
            background-color: #ffeeba;
            color: #856404;
        }
        
        .status-packing {
            background-color: #b8daff;
            color: #004085;
        }
        
        .status-assign {
            background-color: #c3e6cb;
            color: #155724;
        }
        
        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        
        .payment-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .payment-refunded {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .order-items {
            padding: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border: 1px solid #eee;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .item-size, .item-quantity, .item-price {
            font-size: 14px;
            color: #666;
            margin-right: 15px;
        }
        
        .review-button {
            background-color: #0077cc;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .review-button:hover {
            background-color: #005fa3;
        }
        
        .already-reviewed {
            color: #28a745;
            font-size: 14px;
        }
        
        .order-footer {
            background: #f9f9f9;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
        }
        
        .order-actions a {
            margin-left: 10px;
            color: #0077cc;
            text-decoration: none;
        }
        
        .order-actions a:hover {
            text-decoration: underline;
        }
        
        .needs-review {
            background-color: #ffeeba;
            color: #856404;
            padding: 8px 15px;
            font-weight: bold;
            border-radius: 4px;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .needs-review i {
            margin-right: 10px;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }
        
        .no-orders i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="order-history-container">
        <h1 class="page-title">My Order History</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            
            <div class="order-stats">
                <div class="stat-box">
                    <div class="stat-value"><?php echo htmlspecialchars((string)$total_orders); ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
        
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>You haven't placed any orders yet.</p>
                    <a href="../All_Product_Page/all_product.php" class="review-button">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #<?php echo htmlspecialchars((string)$order['order_id']); ?></div>
                                <div class="order-date">
                                    <?php echo date('F j, Y', strtotime($order['order_at'])); ?>
                                </div>
                            </div>
                            <div>
                                <span class="order-total">
                                    RM <?php echo number_format((float)$order['total_price'], 2, '.', ','); ?>
                                </span>
                                <span class="order-status status-<?php echo htmlspecialchars($order['delivery_status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($order['delivery_status'])); ?>
                                </span>
                                <?php if (isset($order['payment_status']) && $order['payment_status']): ?>
                                    <span class="order-status payment-<?php echo htmlspecialchars($order['payment_status']); ?>">
                                        Payment: <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($order['needs_review_count'] > 0): ?>
                            <div class="needs-review">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $order['needs_review_count'] > 1 
                                    ? "You have " . htmlspecialchars((string)$order['needs_review_count']) . " items that need your review!" 
                                    : "You have 1 item that needs your review!"; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image">
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div>
                                            <span class="item-size">Size: <?php echo htmlspecialchars($item['product_size']); ?></span>
                                            <span class="item-quantity">Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></span>
                                            <span class="item-price">RM <?php echo number_format((float)$item['price'], 2, '.', ','); ?></span>
                                        </div>
                                    </div>
                                    <?php if ($item['reviewed'] == 0): ?>
                                        <a href="../Review_Page/write_review.php?product_id=<?php echo htmlspecialchars((string)$item['product_id']); ?>" class="review-button">
                                            Write Review
                                        </a>
                                    <?php else: ?>
                                        <span class="already-reviewed">
                                            <i class="fas fa-check-circle"></i> Reviewed
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-actions">
                                <a href="../Delivery_Status_Page/delivery.php?id=<?php echo htmlspecialchars((string)$order['order_id']); ?>">
                                    <i class="fas fa-truck"></i> Track Order
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html> 