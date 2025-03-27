<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require __DIR__ . '/../app/init.php';

// Stripe API Key
\Stripe\Stripe::setApiKey($stripeSecretKey);
$endpoint_secret = 'whsec_C7rNUfMziKdqbIHkd4Sz2VnFysE5nwSx';

// 创建日志目录（如果不存在）
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

// 日志记录函数
function log_message($level, $message) {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/payment.log');
}

// 读取 Webhook 请求
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'completed', stripe_created = ? WHERE stripe_id = ?");
            $stmt->execute([$paymentIntent->created, $paymentIntent->id]);
            
            $stmt = $pdo->prepare("UPDATE orders SET delivery_status = 'packing' WHERE order_id = ?");
            $stmt->execute([$paymentIntent->metadata->order_id]);
            
            $stmt = $pdo->prepare("INSERT INTO payment_log (payment_id, log_level, log_message) 
                                   SELECT payment_id, 'info', ? FROM payment WHERE stripe_id = ?");
            $logMsg = "支付成功 | Stripe ID: {$paymentIntent->id}";
            $stmt->execute([$logMsg, $paymentIntent->id]);
            
            $pdo->commit();
            log_message('INFO', "Payment success: Order ID: {$paymentIntent->metadata->order_id}");
            break;
            
        case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;
            log_message('ERROR', "Payment failed: Stripe ID: {$paymentIntent->id}");
            break;
    }

    http_response_code(200);

} catch (Exception $e) {
    log_message('ERROR', "Webhook Error: " . $e->getMessage());
    http_response_code(400);
}
