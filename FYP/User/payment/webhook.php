<?php
require_once '/../../vendor/autoload.php';
require_once '../secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
// Replace this endpoint secret with your endpoint's unique secret
// If you are testing with the CLI, find the secret by running 'stripe listen'
// If you are using an endpoint defined with the API or dashboard, look in your webhook settings
// at https://dashboard.stripe.com/webhooks
$endpoint_secret = 'whsec_C7rNUfMziKdqbIHkd4Sz2VnFysE5nwSx';

$payload = @file_get_contents(filename: 'php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
  $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
  
  $pdo = new PDO(dsn: 'mysql:host=localhost;dbname=verosports', username: 'root', password: '');
  
  switch ($event->type) {
      case 'payment_intent.succeeded':
          $paymentIntent = $event->data->object;
          
          $pdo->beginTransaction();
          
          // 更新支付状态
          $stmt = $pdo->prepare(query: "
              UPDATE payment 
              SET payment_status = 'completed', 
                  stripe_created = ? 
              WHERE stripe_id = ?
          ");
          $stmt->execute(params: [
              $paymentIntent->created,
              $paymentIntent->id
          ]);
          
          // 更新订单状态
          $stmt = $pdo->prepare(query: "
              UPDATE orders 
              SET delivery_status = 'packing' 
              WHERE order_id = ?
          ");
          $stmt->execute(params: [
              $paymentIntent->metadata->order_id
          ]);
          
          // 记录日志
          $stmt = $pdo->prepare(query: "
              INSERT INTO payment_log 
              (payment_id, log_level, log_message) 
              SELECT payment_id, 'info', ? 
              FROM payment 
              WHERE stripe_id = ?
          ");
          $logMsg = "支付成功 | Stripe ID: {$paymentIntent->id}";
          $stmt->execute(params: [$logMsg, $paymentIntent->id]);
          
          $pdo->commit();
          break;
          
      case 'payment_intent.payment_failed':
          $paymentIntent = $event->data->object;
          // 类似处理失败逻辑
          break;
  }
  
  http_response_code(response_code: 200);
  
} catch(\Exception $e) {
  error_log(message: 'Webhook Error: ' . $e->getMessage());
  http_response_code(response_code: 400);
}