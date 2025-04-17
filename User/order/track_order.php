<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require __DIR__ . '/../app/init.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];
$error = null;
$orders = [];

try {
    // Initialize Database
    $db = new Database();
    
    // Get specific order if ID is provided
    if (isset($_GET['order_id'])) {
        $order_id = (int)$_GET['order_id'];
        
        // Get order details
        $order = $db->fetchOne(
            "SELECT o.*, 
             (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count,
             (SELECT payment_status FROM payment WHERE order_id = o.order_id ORDER BY payment_at DESC LIMIT 1) as payment_status
             FROM orders o
             WHERE o.order_id = ? AND o.user_id = ?",
            [$order_id, $user_id]
        );
        
        if (!$order) {
            throw new Exception("Order not found or does not belong to you");
        }
        
        // Get order items
        $order['items'] = $db->fetchAll(
            "SELECT oi.*, p.product_name, p.product_img1, p.brand
             FROM order_items oi
             JOIN product p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?
             ORDER BY oi.order_item_id",
            [$order_id]
        );
        
        $orders[] = $order;
    } else {
        // Get all orders for the user
        $orders = $db->fetchAll(
            "SELECT o.*, 
             (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count,
             (SELECT payment_status FROM payment WHERE order_id = o.order_id ORDER BY payment_at DESC LIMIT 1) as payment_status
             FROM orders o
             WHERE o.user_id = ?
             ORDER BY o.order_at DESC",
            [$user_id]
        );
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Function to format price in MYR
function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
}

// Function to get delivery status label
function getStatusLabel($status) {
    $labels = [
        'prepare' => 'Order Preparation',
        'packing' => 'Packing',
        'assign' => 'Carrier Assignment',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered'
    ];
    
    return $labels[$status] ?? 'Unknown';
}

// Function to get delivery status class for CSS
function getStatusClass($status) {
    $classes = [
        'prepare' => 'status-prepare',
        'packing' => 'status-packing',
        'assign' => 'status-assign',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered'
    ];
    
    return $classes[$status] ?? 'status-unknown';
}

// Function to get payment status label
function getPaymentLabel($status) {
    $labels = [
        'pending' => 'Payment Pending',
        'completed' => 'Payment Completed',
        'failed' => 'Payment Failed',
        'refunded' => 'Refunded'
    ];
    
    return $labels[$status] ?? 'Unknown';
}

// Function to get payment status class for CSS
function getPaymentClass($status) {
    $classes = [
        'pending' => 'payment-pending',
        'completed' => 'payment-completed',
        'failed' => 'payment-failed',
        'refunded' => 'payment-refunded'
    ];
    
    return $classes[$status] ?? 'payment-unknown';
}

$pageTitle = isset($_GET['order_id']) ? "Order Details - VeroSports" : "My Orders - VeroSports";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #e60000;
            --secondary-color: #333;
            --light-gray: #f5f5f5;
            --medium-gray: #e0e0e0;
            --dark-gray: #757575;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #f44336;
            --info-color: #2196F3;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
        }
        
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        h1 {
            font-size: 1.8rem;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .orders-container {
            width: 100%;
        }
        
        .order-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--light-gray);
            padding: 1rem;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .order-id {
            font-weight: 600;
            font-size: 1rem;
        }
        
        .order-date {
            color: var(--dark-gray);
            font-size: 0.9rem;
        }
        
        .order-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
        }
        
        .status-prepare {
            background-color: var(--info-color);
            color: white;
        }
        
        .status-packing {
            background-color: var(--warning-color);
            color: #333;
        }
        
        .status-assign {
            background-color: #FF9800;
            color: white;
        }
        
        .status-shipped {
            background-color: #9C27B0;
            color: white;
        }
        
        .status-delivered {
            background-color: var(--success-color);
            color: white;
        }
        
        .payment-pending {
            background-color: var(--warning-color);
            color: #333;
        }
        
        .payment-completed {
            background-color: var(--success-color);
            color: white;
        }
        
        .payment-failed {
            background-color: var(--danger-color);
            color: white;
        }
        
        .payment-refunded {
            background-color: #607D8B;
            color: white;
        }
        
        .order-content {
            padding: 1rem;
        }
        
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .order-items {
            flex: 1;
        }
        
        .order-price {
            font-weight: 600;
            font-size: 1.1rem;
            text-align: right;
        }
        
        .order-address {
            color: var(--dark-gray);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .order-actions {
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--medium-gray);
            padding-top: 1rem;
            margin-top: 0.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #cc0000;
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--secondary-color);
            border: 1px solid var(--medium-gray);
            margin-right: 0.8rem;
        }
        
        .btn-outline:hover {
            background-color: var(--light-gray);
        }
        
        .error-message {
            background-color: #FFEBEE;
            color: var(--danger-color);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .empty-orders {
            text-align: center;
            padding: 3rem 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .empty-orders p {
            margin-bottom: 1.5rem;
            color: var(--dark-gray);
        }
        
        .order-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .order-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            margin-right: 1rem;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        
        .item-variant {
            color: var(--dark-gray);
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        
        .item-price {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--secondary-color);
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .back-link i {
            margin-right: 0.5rem;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        .progress-tracker {
            margin: 2rem 0;
            position: relative;
            display: flex;
            justify-content: space-between;
        }
        
        .progress-step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--medium-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .step-icon.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--medium-gray);
            z-index: 1;
        }
        
        @media (max-width: 768px) {
            .order-header, .order-summary {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-date, .order-price {
                margin-top: 0.5rem;
            }
            
            .progress-tracker {
                flex-direction: column;
                margin-left: 1rem;
            }
            
            .progress-step {
                display: flex;
                align-items: center;
                margin-bottom: 1rem;
                text-align: left;
            }
            
            .step-icon {
                margin: 0 1rem 0 0;
            }
            
            .progress-line {
                top: 0;
                bottom: 0;
                left: 20px;
                width: 2px;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <main>
        <?php if (isset($_GET['order_id']) && !empty($orders)): ?>
            <a href="track_order.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to My Orders</a>
            <h1>Order Details</h1>
        <?php else: ?>
            <h1>My Orders</h1>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="/FYP/User/HomePage/homePage.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                            <div class="order-date"><?php echo date('F j, Y, g:i a', strtotime($order['order_at'])); ?></div>
                            
                            <div>
                                <span class="order-status <?php echo getStatusClass($order['delivery_status']); ?>">
                                    <?php echo getStatusLabel($order['delivery_status']); ?>
                                </span>
                                
                                <?php if (!empty($order['payment_status'])): ?>
                                <span class="order-status <?php echo getPaymentClass($order['payment_status']); ?>">
                                    <?php echo getPaymentLabel($order['payment_status']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="order-content">
                            <?php if (isset($_GET['order_id'])): ?>
                                <!-- Detailed view for a specific order -->
                                <div class="progress-tracker">
                                    <div class="progress-line"></div>
                                    <?php
                                    $statuses = ['prepare', 'packing', 'assign', 'shipped', 'delivered'];
                                    $current_status = $order['delivery_status'];
                                    $current_idx = array_search($current_status, $statuses);
                                    
                                    foreach ($statuses as $idx => $status):
                                        $is_active = $idx <= $current_idx;
                                    ?>
                                    <div class="progress-step">
                                        <div class="step-icon <?php echo $is_active ? 'active' : ''; ?>">
                                            <?php if ($is_active): ?>
                                                <i class="fas fa-check"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="step-label"><?php echo getStatusLabel($status); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="order-address">
                                    <strong>Shipping Address:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                </div>
                                
                                <h3>Order Items</h3>
                                <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo '../../upload/' . htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image">
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div class="item-variant">Size: <?php echo htmlspecialchars($item['product_size']); ?></div>
                                        <div class="item-variant">Quantity: <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div class="item-price"><?php echo formatPrice($item['price'] * $item['quantity']); ?></div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="order-summary" style="margin-top: 1rem; justify-content: flex-end;">
                                    <div class="order-price">
                                        <div>Total: <?php echo formatPrice($order['total_price']); ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Summary view for order list -->
                                <div class="order-summary">
                                    <div class="order-items">
                                        <div><?php echo $order['item_count']; ?> item(s)</div>
                                    </div>
                                    <div class="order-price"><?php echo formatPrice($order['total_price']); ?></div>
                                </div>
                                
                                <div class="order-address">
                                    <strong>Shipping Address:</strong> 
                                    <?php echo htmlspecialchars(substr($order['shipping_address'], 0, 100) . (strlen($order['shipping_address']) > 100 ? '...' : '')); ?>
                                </div>
                                
                                <div class="order-actions">
                                    <a href="track_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html> 