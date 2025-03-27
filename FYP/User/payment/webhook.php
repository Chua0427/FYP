<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require __DIR__ . '/app/init.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
$endpoint_secret = 'whsec_C7rNUfMziKdqbIHkd4Sz2VnFysE5nwSx';

$payload = @file_get_contents(filename: 'php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    $pdo = new PDO(dsn: 'mysql:host=localhost;dbname=verosports', username: 'root', password: '');
    $pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);

    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare(query: "UPDATE payment SET payment_status = 'completed', stripe_created = ? WHERE stripe_id = ?");
            $stmt->execute(params: [$paymentIntent->created, $paymentIntent->id]);
            
            $stmt = $pdo->prepare(query: "UPDATE orders SET delivery_status = 'packing' WHERE order_id = ?");
            $stmt->execute(params: [$paymentIntent->metadata->order_id]);
            
            $stmt = $pdo->prepare(query: "INSERT INTO payment_log (payment_id, log_level, log_message) SELECT payment_id, 'info', ? FROM payment WHERE stripe_id = ?");
            $logMsg = "支付成功 | Stripe ID: {$paymentIntent->id}";
            $stmt->execute(params: [$logMsg, $paymentIntent->id]);
            
            $pdo->commit();
            log_message(level: 'INFO', message: "Payment success: Order ID: {$paymentIntent->metadata->order_id}");
            break;
            
        case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;
            log_message(level: 'ERROR', message: "Payment failed: Stripe ID: {$paymentIntent->id}");
            break;
    }
    http_response_code(response_code: 200);

} catch (Exception $e) {
    log_message(level: 'ERROR', message: "Webhook Error: " . $e->getMessage());
    http_response_code(response_code: 400);
}
?>
