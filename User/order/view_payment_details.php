<?php
declare(strict_types=1);

include __DIR__ . '/../../connect_db/config.php';
require __DIR__ . '/../app/init.php';

// Initialize session if not already started
ensure_session_started();

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
    <link rel="stylesheet" href="view_payment_details.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <a href="#" id="btn-back" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var backBtn = document.getElementById('btn-back');
        if (backBtn) {
            var ref = document.referrer;
            if (ref.includes('orderhistory.php')) {
                backBtn.href = 'orderhistory.php';
            } else if (ref.includes('View_Order/order.php')) {
                backBtn.href = '../View_Order/order.php';
            } else {
                backBtn.href = 'orderhistory.php';
            }
        }
    });
    </script>
</body>
</html> 