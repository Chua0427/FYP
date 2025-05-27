<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';
include __DIR__ . '/../../connect_db/config.php';

// Initialize session if not already started
if (isset($GLOBALS['session_started']) || session_status() === PHP_SESSION_ACTIVE) {
    // Session already started in init.php or elsewhere
} else if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Verify order ID is provided
if (!isset($_GET['order_id'])) {
    echo "Error: Missing order ID";
    exit;
}

// Initialize variables
$order_id = (int)$_GET['order_id'];
$user_id = (int)$_SESSION['user_id'];
$order = null;
$payment = null;
$order_items = [];
$user = null;

try {
    // Verify order belongs to the current user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        echo "Error: Order not found or you don't have permission to view this order.";
        exit;
    }
    
    $order = $order_result->fetch_assoc();
    
    // Get user information
    $stmt = $conn->prepare("SELECT first_name, last_name, email, mobile_number FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Get payment information
    $stmt = $conn->prepare("SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $payment_result = $stmt->get_result();
    
    if ($payment_result->num_rows === 0) {
        echo "Error: No payment information found for this order.";
        exit;
    }
    
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
    
    // Generate HTML content for the PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Invoice #' . $order_id . '</title>
        <style>
            body {
                font-family: "Helvetica", "Arial", sans-serif;
                font-size: 12px;
                line-height: 1.5;
                color: #333;
            }
            
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            }
            
            .invoice-header {
                margin-bottom: 20px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 20px;
            }
            
            .company-details {
                text-align: right;
            }
            
            .invoice-header h1 {
                font-size: 24px;
                margin-bottom: 10px;
                color: #333;
            }
            
            .row {
                display: flex;
                width: 100%;
            }
            
            .col {
                flex: 1;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            
            table th, table td {
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            
            table th {
                background-color: #f8f8f8;
            }
            
            .total-row {
                font-weight: bold;
            }
            
            .payment-info {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
            }
            
            .footer {
                margin-top: 40px;
                text-align: center;
                font-size: 10px;
                color: #777;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .status-completed {
                color: green;
                font-weight: bold;
            }
            
            .status-pending {
                color: orange;
                font-weight: bold;
            }
            
            .status-failed {
                color: red;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="invoice-box">
            <div class="invoice-header">
                <div class="row">
                    <div class="col">
                        <h1>INVOICE</h1>
                        <p>
                            <strong>Invoice #:</strong> ' . $order_id . '<br>
                            <strong>Date:</strong> ' . date("Y-m-d", strtotime($payment['payment_at'])) . '<br>
                            <strong>Payment Status:</strong> <span class="status-' . strtolower($payment['payment_status']) . '">' . ucfirst($payment['payment_status']) . '</span>
                        </p>
                    </div>
                    <div class="col company-details">
                        <h2>VeroSports Sdn. Bhd.</h2>
                        <p>
                            123 Sports Avenue<br>
                            Kuala Lumpur, 50000<br>
                            Malaysia<br>
                            Tel: +603-1234-5678<br>
                            Email: sales@verosports.com
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="customer-details">
                <h3>Bill To:</h3>
                <p>
                    <strong>Name:</strong> ' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '<br>
                    <strong>Email:</strong> ' . htmlspecialchars($user['email']) . '<br>
                    <strong>Phone:</strong> ' . htmlspecialchars($user['mobile_number']) . '<br>
                    <strong>Address:</strong> ' . htmlspecialchars($order['shipping_address']) . '
                </p>
            </div>
            
            <div class="order-details">
                <h3>Order Details:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
                    $subtotal = 0;
                    foreach ($order_items as $item) {
                        $item_total = (float)$item['price'] * (int)$item['quantity'];
                        $subtotal += $item_total;
                        $html .= '
                        <tr>
                            <td>' . htmlspecialchars($item['product_name']) . '</td>
                            <td>' . htmlspecialchars($item['product_size']) . '</td>
                            <td>' . (int)$item['quantity'] . '</td>
                            <td>RM ' . number_format((float)$item['price'], 2) . '</td>
                            <td>RM ' . number_format($item_total, 2) . '</td>
                        </tr>';
                    }
                    
                    $html .= '
                        <tr class="total-row">
                            <td colspan="4" class="text-right">Subtotal:</td>
                            <td>RM ' . number_format($subtotal, 2) . '</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4" class="text-right">Shipping:</td>
                            <td>RM 0.00</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4" class="text-right">Total:</td>
                            <td>RM ' . number_format((float)$payment['total_amount'], 2) . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="payment-info">
                <h3>Payment Information:</h3>
                <p>
                    <strong>Payment ID:</strong> ' . htmlspecialchars($payment['payment_id']) . '<br>
                    <strong>Payment Method:</strong> ' . htmlspecialchars($payment['payment_method']) . '<br>
                    <strong>Transaction ID:</strong> ' . htmlspecialchars($payment['stripe_id']) . '<br>
                    <strong>Payment Date:</strong> ' . date("Y-m-d H:i:s", strtotime($payment['payment_at'])) . '
                </p>
            </div>
            
            <div class="footer">
                <p>This is a computer-generated invoice and doesn\'t require a signature.</p>
                <p>Thank you for shopping at VeroSports!</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Use Dompdf to generate PDF
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Output the generated PDF (force download)
    $dompdf->stream("invoice-" . $order_id . ".pdf", ["Attachment" => false]);
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
} 