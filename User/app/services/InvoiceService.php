<?php
declare(strict_types=1);

namespace App\Services;

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../../payment/db.php';

use Database;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Service for generating and emailing PDF invoices
 */
class InvoiceService
{
    private $db;
    private $logger;
    /**
     * Constructor
     * @param Database $db Database connection
     */
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
        $this->logger = $GLOBALS['logger'];
    }
    
    /**
     * Generate and send invoice for an order
     * 
     * @param int $order_id Order ID
     * @return bool Success status
     */
    public function generateAndSendInvoice(int $order_id): bool
    {
        try {
            $this->logger->info('Starting invoice generation and email for order', ['order_id' => $order_id]);
            error_log("Invoice generation: Starting for order #$order_id");
            
            // Get order details
            $order = $this->db->fetchOne("SELECT o.*, u.email, u.first_name, u.last_name 
                                         FROM orders o
                                         JOIN users u ON o.user_id = u.user_id
                                         WHERE o.order_id = ?", [$order_id]);
            
            if (!$order) {
                $errorMsg = "Order not found: {$order_id}";
                error_log("Invoice generation ERROR: " . $errorMsg);
                throw new \Exception($errorMsg);
            }
            
            error_log("Invoice generation: Order details retrieved for #$order_id, recipient: {$order['email']}");
            
            // Get order items
            $items = $this->db->fetchAll("SELECT oi.*, p.product_name, p.product_img1 
                                        FROM order_items oi 
                                        JOIN product p ON oi.product_id = p.product_id 
                                        WHERE oi.order_id = ?", [$order_id]);
            
            if (empty($items)) {
                $errorMsg = "No items found for order: {$order_id}";
                error_log("Invoice generation ERROR: " . $errorMsg);
                throw new \Exception($errorMsg);
            }
            
            error_log("Invoice generation: " . count($items) . " items retrieved for order #$order_id");
            
            // Get payment information
            $payment = $this->db->fetchOne("SELECT * FROM payment 
                                          WHERE order_id = ? 
                                          ORDER BY payment_at DESC 
                                          LIMIT 1", [$order_id]);
            
            if (!$payment) {
                $errorMsg = "No payment found for order: {$order_id}";
                error_log("Invoice generation ERROR: " . $errorMsg);
                throw new \Exception($errorMsg);
            }
            
            error_log("Invoice generation: Payment info retrieved for order #$order_id, payment ID: {$payment['payment_id']}");
            
            // Generate the PDF
            error_log("Invoice generation: Starting PDF creation");
            $pdfPath = $this->generateInvoicePDF($order, $items, $payment);
            
            if (!file_exists($pdfPath)) {
                error_log("Invoice generation ERROR: PDF file not created at: $pdfPath");
                throw new \Exception("Failed to create PDF file");
            }
            
            error_log("Invoice generation: PDF created at: $pdfPath, size: " . filesize($pdfPath) . " bytes");
            
            // Send the email with PDF attachment
            error_log("Invoice generation: Sending email with PDF attachment");
            $emailSent = $this->sendInvoiceEmail($order, $pdfPath);
            
            // Delete the temporary PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
                error_log("Invoice generation: Temporary PDF file deleted");
            }
            
            if ($emailSent) {
                $this->logger->info('Invoice sent successfully', [
                    'order_id' => $order_id,
                    'email' => $order['email']
                ]);
                error_log("Invoice generation: Successfully completed for order #$order_id");
                
                // Add database log entry for successful email
                try {
                    $this->db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'info', ?)",
                        [$payment['payment_id'], "Invoice email sent successfully to {$order['email']}"]
                    );
                } catch (\Exception $e) {
                    error_log("Invoice generation: Could not log success to database: " . $e->getMessage());
                }
            } else {
                error_log("Invoice generation: Email sending failed for order #$order_id");
                
                // Add database log entry for email failure
                try {
                    $this->db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'error', ?)",
                        [$payment['payment_id'], "Failed to send invoice email to {$order['email']}"]
                    );
                } catch (\Exception $e) {
                    error_log("Invoice generation: Could not log failure to database: " . $e->getMessage());
                }
            }
            
            return $emailSent;
        } catch (\Exception $e) {
            $this->logger->error('Error generating and sending invoice', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            error_log("Invoice generation CRITICAL ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            
            // Try to add a database log entry
            if (isset($payment) && isset($payment['payment_id'])) {
                try {
                    $this->db->execute(
                        "INSERT INTO payment_log (payment_id, log_level, log_message) VALUES (?, 'error', ?)",
                        [$payment['payment_id'], "Invoice generation error: " . $e->getMessage()]
                    );
                } catch (\Exception $logEx) {
                    // Nothing more we can do
                }
            }
            
            return false;
        }
    }
    
    /**
     * Generate invoice PDF
     * 
     * @param array $order Order details
     * @param array $items Order items
     * @param array $payment Payment details
     * @return string Path to the generated PDF file
     */
    private function generateInvoicePDF(array $order, array $items, array $payment): string
    {
        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Prepare HTML content for PDF
        $html = $this->generateInvoiceHTML($order, $items, $payment);
        
        // Load HTML into Dompdf
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render the PDF
        $dompdf->render();
        
        // Generate a temporary file path
        $invoiceNumber = sprintf('INV-%s-%d', date('Ymd'), $order['order_id']);
        $pdfFileName = "{$invoiceNumber}.pdf";
        $pdfPath = sys_get_temp_dir() . '/' . $pdfFileName;
        
        // Save the PDF to the file
        file_put_contents($pdfPath, $dompdf->output());
        
        return $pdfPath;
    }
    
    /**
     * Generate HTML content for the invoice
     * 
     * @param array $order Order details
     * @param array $items Order items
     * @param array $payment Payment details
     * @return string HTML content
     */
    private function generateInvoiceHTML(array $order, array $items, array $payment): string
    {
        $invoiceNumber = sprintf('INV-%s-%d', date('Ymd'), $order['order_id']);
        $orderDate = date('F j, Y', strtotime($order['order_at']));
        $paymentDate = date('F j, Y', strtotime($payment['payment_at']));
        
        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $shipping = $order['total_price'] - $subtotal;
        
        // Build HTML for order items
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemsHtml .= "
                <tr>
                    <td>{$item['product_name']} (Size: {$item['product_size']})</td>
                    <td>{$item['quantity']}</td>
                    <td>RM " . number_format((float)$item['price'], 2) . "</td>
                    <td>RM " . number_format((float)$itemTotal, 2) . "</td>
                </tr>
            ";
        }
        
        // Return the complete HTML
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{$invoiceNumber}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            color: #444;
        }
        .logo {
            margin-bottom: 10px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.invoice-info {
            margin-bottom: 30px;
        }
        table.invoice-info td {
            padding: 5px;
            vertical-align: top;
        }
        table.invoice-items {
            margin-bottom: 30px;
        }
        table.invoice-items th {
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table.invoice-items td {
            border-bottom: 1px solid #eee;
            padding: 10px;
        }
        .totals {
            width: 40%;
            float: right;
        }
        .totals table {
            width: 100%;
        }
        .totals table td {
            padding: 5px;
        }
        .totals table tr.total td {
            border-top: 1px solid #ddd;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            color: #777;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>INVOICE</h1>
        <div>VeroSports</div>
    </div>
    
    <table class="invoice-info">
        <tr>
            <td width="50%">
                <strong>Billed To:</strong><br>
                {$order['first_name']} {$order['last_name']}<br>
                Email: {$order['email']}<br>
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Invoice Number:</strong> {$invoiceNumber}<br>
                <strong>Order ID:</strong> {$order['order_id']}<br>
                <strong>Order Date:</strong> {$orderDate}<br>
                <strong>Payment Date:</strong> {$paymentDate}<br>
                <strong>Payment Method:</strong> {$payment['payment_method']}<br>
            </td>
        </tr>
    </table>
    
    <h3>Items Purchased</h3>
    <table class="invoice-items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {$itemsHtml}
        </tbody>
    </table>
    
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">RM {$subtotal}</td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td style="text-align: right;">RM {$shipping}</td>
            </tr>
            <tr class="total">
                <td>Total:</td>
                <td style="text-align: right;">RM {$order['total_price']}</td>
            </tr>
        </table>
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>For any questions regarding this invoice, please contact support at support@verosports.com</p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Send invoice by email
     * 
     * @param array $order Order details
     * @param string $pdfPath Path to the PDF file
     * @return bool Success status
     */
    private function sendInvoiceEmail(array $order, string $pdfPath): bool
    {
        // Create a direct log file to capture all debugging
        $logFile = __DIR__ . '/../../logs/invoice_email_debug_' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Starting email process for order #{$order['order_id']}\n", FILE_APPEND);
        $this->logToFile($logFile, "PHP version: " . phpversion());
        
        // Quick test to see if basic mail function works
        $testMailResult = @mail('chiannchua05@gmail.com', 'Test from InvoiceService', 'This is a test message', 'From: chiannchua05@gmail.com');
        $this->logToFile($logFile, "Basic mail() function test: " . ($testMailResult ? "SUCCESS" : "FAILED"));
        
        // PHP configuration check
        $smtpHost = ini_get('SMTP');
        $smtpPort = ini_get('smtp_port');
        $sendmailPath = ini_get('sendmail_path');
        $this->logToFile($logFile, "PHP Mail Configuration: SMTP=$smtpHost, Port=$smtpPort, Sendmail=$sendmailPath");
        
        try {
            // Define PHPMailer paths
            $exceptionPath = __DIR__ . '/../../ForgotPassword/phpmailer/src/Exception.php';
            $phpmailerPath = __DIR__ . '/../../ForgotPassword/phpmailer/src/PHPMailer.php';
            $smtpPath = __DIR__ . '/../../ForgotPassword/phpmailer/src/SMTP.php';
            
            // Check if PHPMailer files exist
            if (!file_exists($exceptionPath) || !file_exists($phpmailerPath) || !file_exists($smtpPath)) {
                error_log("Invoice email: PHPMailer files not found at expected paths");
                $this->logToFile($logFile, "ERROR: PHPMailer files not found at: " . dirname($exceptionPath));
                $this->logger->error('PHPMailer files not found', [
                    'paths_checked' => [dirname($exceptionPath)]
                ]);
                return false;
            }
            
            // Include PHPMailer classes
            require_once $exceptionPath;
            require_once $phpmailerPath;
            require_once $smtpPath;
            
            // Debug logging
            error_log("Invoice email: Starting email preparation for order #{$order['order_id']}");
            $this->logToFile($logFile, "Loading PHPMailer classes successful");
            
            $mail = new PHPMailer(true);
            $this->logToFile($logFile, "PHPMailer object created");
            
            // Enable debugging
            $mail->SMTPDebug = 3; // More detailed debug output
            $mail->Debugoutput = function($str, $level) {
                $GLOBALS['paymentLogger']->debug("PHPMailer debug [{$level}]: {$str}");
            };
            
            try {
                // Use SMTP directly with Gmail - Don't try localhost first
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chiannchua05@gmail.com'; // Use your email
                $mail->Password = 'niiwzkwxnqlecaww'; // Updated app password format
                // Try STARTTLS (port 587) first as it's more reliable
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Set timeout for SMTP connection
                $mail->Timeout = 60; // Increase timeout to 60 seconds
                
                error_log("Invoice email: SMTP settings configured for {$mail->Host}");
                error_log("Invoice email: Using SMTP with username: {$mail->Username}");
                error_log("Invoice email: Using SMTP with port: {$mail->Port}");
                $this->logToFile($logFile, "SMTP configuration: Host={$mail->Host}, User={$mail->Username}, Port={$mail->Port}, Secure={$mail->SMTPSecure}");
                
                // Try to validate connection before proceeding
                try {
                    $this->logToFile($logFile, "Testing SMTP connection before sending...");
                    $mail->smtpConnect();
                    $this->logToFile($logFile, "SMTP connection test successful");
                } catch (Exception $connError) {
                    $this->logToFile($logFile, "SMTP connection test failed: " . $connError->getMessage());
                    
                    // Try alternate port
                    $this->logToFile($logFile, "Trying alternate Gmail SMTP port 587...");
                    $mail->Port = 587;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    
                    try {
                        $mail->smtpConnect();
                        $this->logToFile($logFile, "Alternate SMTP connection successful");
                    } catch (Exception $altConnError) {
                        $this->logToFile($logFile, "Alternate SMTP connection also failed: " . $altConnError->getMessage());
                        throw $altConnError; // Let it fall back to mail()
                    }
                }
            } catch (Exception $e) {
                // If SMTP setup fails, fall back to mail()
                error_log("Invoice email: SMTP setup failed, falling back to mail(): " . $e->getMessage());
                $this->logToFile($logFile, "SMTP setup failed: " . $e->getMessage());
                $mail->isMail();
            }
            
            // Recipients
            $mail->setFrom('chiannchua05@gmail.com', 'VeroSports');
            $mail->addAddress($order['email'], $order['first_name'] . ' ' . $order['last_name']);
            error_log("Invoice email: Recipient set to {$order['email']}");
            $this->logToFile($logFile, "Recipients configured: From=chiannchua05@gmail.com, To={$order['email']}");
            
            // Generate invoice filename
            $invoiceNumber = sprintf('INV-%s-%d', date('Ymd'), $order['order_id']);
            $pdfFileName = "{$invoiceNumber}.pdf";
            
            // Check if file exists
            if (!file_exists($pdfPath)) {
                error_log("Invoice email: PDF file not found at {$pdfPath}");
                $this->logToFile($logFile, "ERROR: PDF file not found at {$pdfPath}");
                return false;
            }
            
            error_log("Invoice email: PDF file exists at {$pdfPath}, size: " . filesize($pdfPath) . " bytes");
            $this->logToFile($logFile, "PDF file found: {$pdfPath}, Size: " . filesize($pdfPath) . " bytes");
            
            // Attachments
            $mail->addAttachment($pdfPath, $pdfFileName);
            error_log("Invoice email: PDF attachment added");
            $this->logToFile($logFile, "PDF attachment added successfully");
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your VeroSports Invoice #{$invoiceNumber}";
            $mail->Body = $this->generateEmailBody($order);
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $this->generateEmailBody($order)));
            $this->logToFile($logFile, "Email content prepared, Subject: {$mail->Subject}");
            
            error_log("Invoice email: Attempting to send to {$order['email']}");
            $this->logToFile($logFile, "Attempting to send email now...");
            
            // Send the email
            try {
                $result = $mail->send();
                error_log("Invoice email: Send result: " . ($result ? "SUCCESS" : "FAILED"));
                $this->logToFile($logFile, "Send result: " . ($result ? "SUCCESS" : "FAILED"));
                
                if (!$result) {
                    // Try direct PHP mail() as a last resort
                    $this->logToFile($logFile, "SMTP failed, trying direct email method");
                    return $this->sendDirectEmail($order);
                }
                
                return $result;
            } catch (Exception $e) {
                $this->logToFile($logFile, "Send exception: " . $e->getMessage());
                if (isset($mail->ErrorInfo)) {
                    $this->logToFile($logFile, "PHPMailer error: " . $mail->ErrorInfo);
                }
                throw $e;
            }
        } catch (Exception $e) {
            error_log("Invoice email ERROR: " . $e->getMessage());
            $this->logToFile($logFile, "CRITICAL ERROR: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            if (isset($mail) && isset($mail->ErrorInfo)) {
                error_log("PHPMailer ERROR: " . $mail->ErrorInfo);
                $this->logToFile($logFile, "PHPMailer ERROR: " . $mail->ErrorInfo);
            }
            $this->logger->error('Email sending error', [
                'error' => isset($mail) ? $mail->ErrorInfo : $e->getMessage(),
                'order_id' => $order['order_id'],
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try direct PHP mail() as a last resort
            try {
                error_log("Invoice email: Attempting to send using PHP mail() function as last resort");
                $to = $order['email'];
                $subject = "Your VeroSports Invoice";
                $message = "Thank you for your purchase. Please contact support@verosports.com to request your invoice.";
                $headers = "From: chiannchua05@gmail.com\r\n";
                $headers .= "Reply-To: chiannchua05@gmail.com\r\n";
                $result = mail($to, $subject, $message, $headers);
                error_log("PHP mail() result: " . ($result ? "SUCCESS" : "FAILED"));
                
                if (!$result) {
                    // Try direct socket method as final fallback
                    return $this->sendDirectEmail($order);
                }
                
                return $result;
            } catch (Exception $mailError) {
                error_log("PHP mail() error: " . $mailError->getMessage());
                return false;
            }
        }
    }
    
    /**
     * Generate HTML content for email body
     * 
     * @param array $order Order details
     * @return string HTML content
     */
    private function generateEmailBody(array $order): string
    {
        $invoiceNumber = sprintf('INV-%s-%d', date('Ymd'), $order['order_id']);
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee;'>
                <h2>VeroSports</h2>
            </div>
            
            <div style='padding: 20px 0;'>
                <h3>Thank you for your purchase!</h3>
                <p>Dear {$order['first_name']} {$order['last_name']},</p>
                
                <p>Thank you for shopping with VeroSports. Your order has been successfully processed and paid.</p>
                
                <p>Please find attached your invoice #{$invoiceNumber} for your records.</p>
                
                <p>Order ID: {$order['order_id']}<br>
                Order Date: " . date('F j, Y', strtotime($order['order_at'])) . "<br>
                Total Amount: RM " . number_format((float)$order['total_price'], 2) . "</p>
                
                <p>If you have any questions regarding your order or invoice, please don't hesitate to contact our customer service team.</p>
            </div>
            
            <div style='padding-top: 20px; border-top: 1px solid #eee; color: #777; font-size: 12px;'>
                <p>This is an automated email, please do not reply to this message.</p>
            </div>
        </div>
        ";
    }
    
    /**
     * Direct socket-based mailer as final fallback
     * 
     * @param array $order Order details
     * @return bool Success status
     */
    private function sendDirectEmail(array $order): bool
    {
        // Create a direct log file
        $logFile = __DIR__ . '/../../logs/direct_email_' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->logToFile($logFile, "Starting direct email sending for order #{$order['order_id']}");
        
        try {
            error_log("Direct email: Attempting to send using direct socket connection");
            $this->logToFile($logFile, "Attempting to send using direct methods");
            
            $to = $order['email'];
            $from = 'chiannchua05@gmail.com';
            $subject = "Your VeroSports Order Confirmation";
            
            $message = "Thank you for your purchase with VeroSports!\r\n\r\n";
            $message .= "Order ID: {$order['order_id']}\r\n";
            $message .= "Order Date: " . date('F j, Y', strtotime($order['order_at'])) . "\r\n";
            $message .= "Total Amount: RM " . number_format((float)$order['total_price'], 2) . "\r\n\r\n";
            $message .= "For any questions regarding your order, please contact our customer service team.\r\n";
            
            $headers = "From: $from\r\n";
            $headers .= "Reply-To: $from\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            
            $this->logToFile($logFile, "Email prepared: To=$to, From=$from, Subject=$subject");
            
            // First try mail()
            if(function_exists('mail')) {
                error_log("Direct email: Trying PHP mail() function");
                $this->logToFile($logFile, "Attempting to use PHP mail() function");
                $result = mail($to, $subject, $message, $headers);
                if($result) {
                    error_log("Direct email: PHP mail() succeeded");
                    $this->logToFile($logFile, "PHP mail() SUCCEEDED");
                    return true;
                }
                error_log("Direct email: PHP mail() failed");
                $this->logToFile($logFile, "PHP mail() FAILED");
            } else {
                $this->logToFile($logFile, "PHP mail() function not available");
            }
            
            // Fall back to SMTP via sockets if available
            if(function_exists('fsockopen')) {
                error_log("Direct email: Trying direct SMTP via socket");
                $this->logToFile($logFile, "Attempting SMTP via socket connection");
                
                // Try connecting to various SMTP servers
                $smtpServers = [
                    ['localhost', 25],
                    ['127.0.0.1', 25],
                    ['smtp.gmail.com', 587],
                    ['smtp.gmail.com', 465],
                    ['mail.yourdomain.com', 25] // Replace with any local mail server
                ];
                
                foreach ($smtpServers as $server) {
                    list($smtpServer, $smtpPort) = $server;
                    $timeout = 10; // Shorter timeout for quick testing
                    
                    $this->logToFile($logFile, "Trying SMTP server: $smtpServer:$smtpPort");
                    
                    // Connect to SMTP server
                    $errno = 0;
                    $errstr = '';
                    $smtpSocket = @fsockopen($smtpServer, $smtpPort, $errno, $errstr, $timeout);
                    if (!$smtpSocket) {
                        error_log("Direct email: Socket connection failed: $errstr ($errno)");
                        $this->logToFile($logFile, "Socket connection FAILED: $errstr ($errno)");
                        continue; // Try next server
                    }
                    
                    $this->logToFile($logFile, "Socket connection established to $smtpServer:$smtpPort");
                    
                    // Try simple SMTP protocol
                    try {
                        // Read server greeting
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "Server greeting: $response");
                        if (substr($response, 0, 3) != '220') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Send HELO command
                        $hostname = php_uname('n');
                        $this->logToFile($logFile, "Sending HELO $hostname");
                        fputs($smtpSocket, "HELO $hostname\r\n");
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "HELO response: $response");
                        if (substr($response, 0, 3) != '250') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Set sender
                        $this->logToFile($logFile, "Setting sender: $from");
                        fputs($smtpSocket, "MAIL FROM: <$from>\r\n");
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "MAIL FROM response: $response");
                        if (substr($response, 0, 3) != '250') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Set recipient
                        $this->logToFile($logFile, "Setting recipient: $to");
                        fputs($smtpSocket, "RCPT TO: <$to>\r\n");
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "RCPT TO response: $response");
                        if (substr($response, 0, 3) != '250' && substr($response, 0, 3) != '251') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Begin data
                        $this->logToFile($logFile, "Sending DATA command");
                        fputs($smtpSocket, "DATA\r\n");
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "DATA response: $response");
                        if (substr($response, 0, 3) != '354') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Send headers and message
                        $this->logToFile($logFile, "Sending email content");
                        fputs($smtpSocket, "To: $to\r\n");
                        fputs($smtpSocket, "From: $from\r\n");
                        fputs($smtpSocket, "Subject: $subject\r\n");
                        fputs($smtpSocket, $headers . "\r\n");
                        fputs($smtpSocket, $message . "\r\n.\r\n");
                        
                        $response = fgets($smtpSocket, 515);
                        $this->logToFile($logFile, "Message sending response: $response");
                        if (substr($response, 0, 3) != '250') {
                            fclose($smtpSocket);
                            continue; // Try next server
                        }
                        
                        // Terminate connection
                        fputs($smtpSocket, "QUIT\r\n");
                        fclose($smtpSocket);
                        
                        error_log("Direct email: Socket SMTP succeeded with $smtpServer:$smtpPort");
                        $this->logToFile($logFile, "Socket SMTP SUCCESS with $smtpServer:$smtpPort");
                        return true;
                    } catch (Exception $e) {
                        $this->logToFile($logFile, "SMTP protocol error with $smtpServer:$smtpPort: " . $e->getMessage());
                        if ($smtpSocket) {
                            fclose($smtpSocket);
                        }
                        continue; // Try next server
                    }
                }
                
                $this->logToFile($logFile, "All SMTP servers failed");
            } else {
                $this->logToFile($logFile, "fsockopen function not available");
            }
            
            // Last resort: Try other mail libraries/methods if available
            $this->logToFile($logFile, "All direct email methods failed");
            error_log("Direct email: All methods failed");
            return false;
        } catch (Exception $e) {
            error_log("Direct email ERROR: " . $e->getMessage());
            $this->logToFile($logFile, "CRITICAL ERROR: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return false;
        }
    }
    private function logToFile($logFile, $message)
    {
        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " $message\n", FILE_APPEND);
    }
} 