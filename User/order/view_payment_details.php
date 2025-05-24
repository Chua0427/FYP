<?php
declare(strict_types=1);

include __DIR__ . '/../../connect_db/config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$error_message = '';
$payment = null;
$order = null;
$order_items = [];

if (!isset($_GET['order_id'])) {
    $error_message = 'Invalid request. No order ID specified.';
} else {
    $order_id = (int)$_GET['order_id'];
    
    // Verify order belongs to the current user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        $error_message = 'Order not found or you do not have permission to view this order.';
    } else {
        $order = $order_result->fetch_assoc();
        
        // Get payment information
        $stmt = $conn->prepare("SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $payment_result = $stmt->get_result();
        
        if ($payment_result->num_rows > 0) {
            $payment = $payment_result->fetch_assoc();
            
            // Get order items
            $stmt = $conn->prepare("
                SELECT oi.*, p.product_name, p.product_img1
                FROM order_items oi
                JOIN product p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?
            ");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $error_message = 'No payment information found for this order.';
        }
    }
}

// Function to format status with appropriate styling
function formatStatus(string $status): string {
    $statusClass = '';
    switch (strtolower($status)) {
        case 'completed':
            $statusClass = 'status-completed';
            break;
        case 'pending':
            $statusClass = 'status-pending';
            break;
        case 'failed':
            $statusClass = 'status-failed';
            break;
        case 'refunded':
            $statusClass = 'status-refunded';
            break;
        default:
            $statusClass = 'status-unknown';
    }
    
    return '<span class="payment-status ' . $statusClass . '">' . ucfirst(htmlspecialchars($status)) . '</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            margin: 0;
            background: #f8f8f8;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 120px 20px 150px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
        }
        
        .payment-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .payment-header {
            background: #f0f0f0;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .payment-header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .payment-body {
            padding: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .detail-label {
            width: 200px;
            font-weight: bold;
            color: #666;
        }
        
        .detail-value {
            flex: 1;
            color: #333;
        }
        
        .order-items-list {
            margin-top: 30px;
        }
        
        .order-items-list h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .order-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .item-details {
            display: flex;
            font-size: 14px;
            color: #666;
        }
        
        .item-detail {
            margin-right: 15px;
        }
        
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: #e64a19;
        }
        
        .btn-back {
            background-color: #757575;
        }
        
        .btn-back:hover {
            background-color: #616161;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #b71c1c;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #ff6f00;
        }
        
        .status-failed {
            background-color: #ffebee;
            color: #b71c1c;
        }
        
        .status-refunded {
            background-color: #e0f2f1;
            color: #00796b;
        }
        
        .status-unknown {
            background-color: #f5f5f5;
            color: #616161;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="container">
        <h1 class="page-title">Payment Details</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif ($payment && $order): ?>
            <div class="payment-container">
                <div class="payment-header">
                    <h2>Payment Information</h2>
                    <div>Order #<?php echo htmlspecialchars((string)$order['order_id']); ?></div>
                </div>
                <div class="payment-body">
                    <div class="detail-row">
                        <div class="detail-label">Payment ID</div>
                        <div class="detail-value"><?php echo htmlspecialchars($payment['payment_id']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value"><?php echo formatStatus($payment['payment_status']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Amount</div>
                        <div class="detail-value">RM <?php echo number_format((float)$payment['total_amount'], 2); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value"><?php echo htmlspecialchars($payment['payment_method']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Transaction ID</div>
                        <div class="detail-value"><?php echo htmlspecialchars($payment['stripe_id']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Currency</div>
                        <div class="detail-value"><?php echo htmlspecialchars($payment['currency']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Date & Time</div>
                        <div class="detail-value"><?php echo date("Y-m-d H:i:s", strtotime($payment['payment_at'])); ?></div>
                    </div>
                    
                    <?php if (!empty($order['shipping_address'])): ?>
                    <div class="detail-row">
                        <div class="detail-label">Shipping Address</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="order-items-list">
                <h3>Ordered Items</h3>
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image">
                        <div class="item-info">
                            <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div class="item-details">
                                <div class="item-detail">Size: <?php echo htmlspecialchars($item['product_size']); ?></div>
                                <div class="item-detail">Quantity: <?php echo htmlspecialchars((string)$item['quantity']); ?></div>
                                <div class="item-detail">Price: RM <?php echo number_format((float)$item['price'], 2); ?></div>
                                <div class="item-detail">Subtotal: RM <?php echo number_format((float)$item['price'] * (int)$item['quantity'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="actions">
                <a href="gen_pdf_bill.php?order_id=<?php echo htmlspecialchars((string)$order['order_id']); ?>" class="btn" target="_blank">
                    <i class="fas fa-print"></i> Print Bill
                </a>
                <a href="orderhistory.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html> 