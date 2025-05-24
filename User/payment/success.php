<?php
// Set maximum error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/success_errors.log');

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/services/OrderService.php';

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Log page access for debugging
file_put_contents($logDir . '/success_page.log', date('[Y-m-d H:i:s]') . " Success page accessed with REQUEST_URI: " . $_SERVER['REQUEST_URI'] . PHP_EOL, FILE_APPEND);

// Test mail functionality directly
$mailWorkingFile = __DIR__ . '/logs/mail_test.log';
$mailResult = @mail('chiannchua05@gmail.com', 'Mail Test', 'This is a test email from success.php', 'From: chiannchua05@gmail.com');
file_put_contents($mailWorkingFile, date('[Y-m-d H:i:s]') . " Mail test result: " . ($mailResult ? "SUCCESS" : "FAILED") . PHP_EOL, FILE_APPEND);

// Initialize Database
$db = new Database();

// Debug log function for stock updates
function debug_log($message, $data = []) {
    $log_file = __DIR__ . '/logs/success_debug.log';
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
    // Get order_id from URL parameter - log all possible sources
    debug_log("URL parameters received", [
        'GET' => $_GET,
        'REQUEST_URI' => $_SERVER['REQUEST_URI'],
        'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'none'
    ]);
    
    // Get order_id from URL parameter
    $order_id = $_GET['order_id'] ?? '';
    
    if (empty($order_id)) {
        // Try to parse from query string if direct GET access failed
        if (!empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $params);
            if (isset($params['order_id'])) {
                $order_id = $params['order_id'];
            }
        }
    }
    
    debug_log("Processing order", ['order_id' => $order_id]);

    if (empty($order_id)) {
        throw new Exception('Missing required order ID parameter');
    }

    // Get order details
    $order = $db->fetchOne("SELECT * FROM orders WHERE order_id = ?", [$order_id]);
    if (!$order) {
        throw new Exception('Order not found');
    }

    // Get payment details by order ID
    $payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1",
        [$order_id]
    );

    if (!$payment) {
        throw new Exception('Payment record not found');
    }

    // Update payment status if needed
    if ($payment['payment_status'] !== 'completed') {
        $db->beginTransaction();
        
        try {
            $db->execute(
                "UPDATE payment SET payment_status = 'completed' WHERE payment_id = ?",
                [$payment['payment_id']]
            );
            
            // Update order status to prepare instead of packing
            $db->execute(
                "UPDATE orders SET delivery_status = 'prepare' WHERE order_id = ?",
                [$order_id]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment marked as completed via success page"]
            );
            
            // Generate and send invoice after commit
            $db->commit();

            try {
                // Include the InvoiceService class
                require_once __DIR__ . '/../app/services/InvoiceService.php';
                
                // Create an instance of the InvoiceService
                $invoiceService = new \App\Services\InvoiceService($db);
                
                // Generate and send the invoice
                $invoiceSent = $invoiceService->generateAndSendInvoice((int)$order_id);
                
                // Log the result
                if ($invoiceSent) {
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                        [$payment['payment_id'], "Invoice PDF generated and sent to customer via email"]
                    );
                } else {
                    $db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'warning', ?)",
                        [$payment['payment_id'], "Failed to generate and send invoice PDF"]
                    );
                }
            } catch (\Exception $invoiceError) {
                // Log invoice error but don't interrupt the flow
                $db->execute(
                    "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'error', ?)",
                    [$payment['payment_id'], "Invoice error: " . $invoiceError->getMessage()]
                );
                error_log("Invoice generation error for order #$order_id: " . $invoiceError->getMessage());
            }
        } catch (Exception $e) {
            if ($db->isTransactionActive()) {
                $db->rollback();
            }
            
            // Log error but continue showing the success page
            error_log("Error updating payment status: " . $e->getMessage());
            
            // Still show success page even if updating payment status fails
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
    
    // Calculate total price (redundant with order.total_price, but for safety)
    $totalItems = 0;
    $totalPrice = 0;
    foreach ($orderItems as $item) {
        $totalItems += $item['quantity'];
        $totalPrice += $item['price'] * $item['quantity'];
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Payment success page error: " . $e->getMessage());
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
            <p>A receipt has been sent to your email address.</p>
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
                        <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['product_img1'])): ?>
                                    <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" 
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
            <a href="../HomePage/homePage.php" class="btn btn-primary">Continue Shopping</a>
            <a href="../View_Order/view_order.php" class="btn btn-secondary ms-2">View Orders</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Direct manual test for invoice generation (only runs if there's a valid order)
if (isset($order_id) && !empty($order_id) && is_numeric($order_id)) {
    try {
        // Create direct test log
        $testLogFile = __DIR__ . '/logs/invoice_test_' . date('Y-m-d') . '.log';
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " Starting direct invoice test for order #$order_id\n", FILE_APPEND);
        
        // Check configuration
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " PHP Version: " . phpversion() . "\n", FILE_APPEND);
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " SMTP: " . ini_get('SMTP') . "\n", FILE_APPEND);
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " SMTP Port: " . ini_get('smtp_port') . "\n", FILE_APPEND);
        
        // Load dependent classes
        require_once __DIR__ . '/../app/services/InvoiceService.php';
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " InvoiceService class loaded\n", FILE_APPEND);
        
        // Create an instance of the invoice service
        $invoiceService = new \App\Services\InvoiceService($db);
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " InvoiceService instantiated\n", FILE_APPEND);
        
        // Generate and send invoice
        $result = $invoiceService->generateAndSendInvoice((int)$order_id);
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " Invoice generation result: " . ($result ? "SUCCESS" : "FAILED") . "\n", FILE_APPEND);
    } catch (\Exception $e) {
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        file_put_contents($testLogFile, date('[Y-m-d H:i:s]') . " Trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
    }
}
?> 