<?php
require __DIR__ . '/../../../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51R3yBQQZPLk7FzRY74j8818Wi2KEMJ4GeHDJAAGhxaEyeFBHEjLrTcY8uzB4v7kvd6yuKCgd4gQIqKArIhfNPIgy00Gktm1Et6');

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$endpoint_secret = 'whsec_your_webhook_secret';

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );

  switch ($event->type) {
    case 'payment_intent.succeeded':
      $paymentIntent = $event->data->object;
      // 更新数据库
      $pdo->prepare("UPDATE orders SET status='paid' WHERE stripe_id=?")
          ->execute([$paymentIntent->id]);
      break;
  }

  http_response_code(200);
} catch(Exception $e) {
  http_response_code(400);
}