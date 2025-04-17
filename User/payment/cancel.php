<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

// Ensure logs directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Function to log messages
function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/payment.log');
}

// Initialize database
$db = new Database();

try {
    // Get order_id from URL parameters
    $order_id = $_GET['order_id'] ?? '';

    if (empty($order_id)) {
        throw new Exception('Missing order ID');
    }
    
    // Get order details
    $order = $db->fetchOne("SELECT * FROM orders WHERE order_id = ?", [$order_id]);
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Check for payment
    $payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1", 
        [$order_id]
    );

    // If there's a payment, update its status if it's not already failed
    if ($payment && $payment['payment_status'] !== 'failed') {
        $db->beginTransaction();
        
        // Update payment status to failed
        $db->execute(
            "UPDATE payment SET payment_status = 'failed', last_error = 'Payment cancelled by user' WHERE payment_id = ?",
            [$payment['payment_id']]
        );
        
        // Add log entry
        $db->execute(
            "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
            [$payment['payment_id'], "Payment cancelled by user"]
        );
        
        $db->commit();
    }
    
    // Log cancellation
    log_message('INFO', "Payment cancelled for Order ID: $order_id");
    
} catch (Exception $e) {
    // Log error
    log_message('ERROR', "Payment cancellation error: " . $e->getMessage());
    
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cancel-icon {
            color: #dc3545;
            font-size: 4rem;
        }
        .cancel-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="cancel-container text-center">
            <i class="bi bi-x-circle-fill cancel-icon"></i>
            <h1 class="mt-3">Payment Cancelled</h1>
            <p class="lead mb-4">Your payment has been cancelled. No charges have been made to your account.</p>
            
            <div class="mt-4">
                <a href="../index.php" class="btn btn-primary">Return to Home</a>
                <?php if (isset($order_id) && !empty($order_id)): ?>
                <a href="../checkout.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-outline-secondary ms-2">Try Again</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 