<?php
declare(strict_types=1);
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

$error_code = '';
$error_message = '';
$suggestions = [];
$order_id = '';

try {
    // Get order_id from URL parameters
    $order_id = $_GET['order_id'] ?? '';
    $error_code = $_GET['error_code'] ?? '';

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

    // If there's a payment, get the error details
    if ($payment && ($payment['payment_status'] === 'failed' || $payment['last_error'])) {
        $error_message = $payment['last_error'] ?? 'Payment was unsuccessful';
        
        // Update payment status to failed if it's not already
        if ($payment['payment_status'] !== 'failed') {
            $db->beginTransaction();
            
            // Update payment status to failed
            $db->execute(
                "UPDATE payment SET payment_status = 'failed', last_error = ? WHERE payment_id = ?",
                ['Payment cancelled or failed', $payment['payment_id']]
            );
            
            // Add log entry
            $db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                [$payment['payment_id'], "Payment cancelled by user or system"]
            );
            
            $db->commit();
        }
    } else {
        // Generic error message if no specific payment record found
        $error_message = 'Your payment was cancelled or failed to complete';
    }
    
    // Provide helpful suggestions based on error code or message
    if ($error_code === 'card_declined' || stripos($error_message, 'declined') !== false) {
        $suggestions = [
            'Check if your card has sufficient funds',
            'Verify that your card is not expired or blocked',
            'Contact your bank to authorize online transactions',
            'Try using a different payment card'
        ];
    } else if ($error_code === 'expired_card' || stripos($error_message, 'expire') !== false) {
        $suggestions = [
            'Your card appears to be expired',
            'Please use a different, valid payment card',
            'Update your card details with your bank'
        ];
    } else if ($error_code === 'invalid_cvc' || stripos($error_message, 'cvc') !== false || stripos($error_message, 'security code') !== false) {
        $suggestions = [
            'Check that you entered the correct security code (CVC)',
            'The 3-4 digit code is located on the back of your card (or front for American Express)'
        ];
    } else if ($error_code === 'processing_error' || stripos($error_message, 'processing') !== false) {
        $suggestions = [
            'There was a temporary processing error',
            'Please wait a few minutes and try again',
            'Check that your internet connection is stable'
        ];
    } else if ($error_code === 'rate_limit' || stripos($error_message, 'rate limit') !== false) {
        $suggestions = [
            'Too many payment attempts in a short period',
            'Please wait a few minutes before trying again'
        ];
    } else if ($error_code === 'invalid_number' || stripos($error_message, 'card number') !== false) {
        $suggestions = [
            'The card number you entered is invalid',
            'Double-check your card number and try again',
            'Try using a different payment card'
        ];
    } else {
        // Default suggestions
        $suggestions = [
            'Double-check your payment information and try again',
            'Try using a different payment card',
            'Check with your bank if there are any restrictions on your card',
            'Ensure your internet connection is stable during checkout'
        ];
    }
    
    // Log cancellation
    log_message('INFO', "Payment cancelled for Order ID: $order_id, Error: " . ($error_code ?: 'none'));
    
} catch (Exception $e) {
    // Log error
    log_message('ERROR', "Payment cancellation error: " . $e->getMessage());
    
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    // Default error message
    $error_message = 'An unexpected error occurred during payment processing';
    $suggestions = [
        'Please try again later',
        'Contact customer support if the problem persists'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - VeroSports</title>
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
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .cancel-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        
        .cancel-icon {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #222;
        }
        
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .suggestions {
            margin: 30px 0;
            text-align: left;
        }
        
        .suggestions h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #222;
        }
        
        .suggestions ul {
            list-style-type: none;
            padding-left: 10px;
        }
        
        .suggestions li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 30px;
        }
        
        .suggestions li::before {
            content: "\f05a";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #17a2b8;
            position: absolute;
            left: 0;
            top: 2px;
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 12px 24px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            margin: 0 8px 10px;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        .btn-outline {
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        
        .btn-outline:hover {
            color: #fff;
            background-color: #6c757d;
        }
        
        .contact-support {
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .contact-support a {
            color: #007bff;
            text-decoration: none;
        }
        
        .contact-support a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .cancel-container {
                padding: 25px 15px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .btn {
                display: block;
                width: 100%;
                margin: 0 0 15px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
        
        <main>
            <div class="container">
                <div class="cancel-container">
                    <i class="fas fa-times-circle cancel-icon"></i>
                    <h1>Payment Unsuccessful</h1>
                    <p class="lead">Your payment was not completed.</p>
                    
                    <?php if ($error_message): ?>
                        <div class="error-message">
                            <strong><i class="fas fa-exclamation-triangle"></i> Error:</strong> 
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="suggestions">
                        <h2>Suggestions to Resolve the Issue:</h2>
                        <ul>
                            <?php foreach ($suggestions as $suggestion): ?>
                                <li><?php echo htmlspecialchars($suggestion); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="actions">
                        <a href="process_payment.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-primary">
                            <i class="fas fa-redo-alt"></i> Try Again
                        </a>
                        <a href="payment_methods.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-outline">
                            <i class="fas fa-credit-card"></i> Change Payment Method
                        </a>
                        <a href="../index.php" class="btn btn-outline">
                            <i class="fas fa-home"></i> Return to Home
                        </a>
                    </div>
                    
                    <p class="contact-support">
                        Need help? <a href="../contact_us.php">Contact our support team</a>
                    </p>
                </div>
            </div>
        </main>
        
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    </div>
</body>
</html> 