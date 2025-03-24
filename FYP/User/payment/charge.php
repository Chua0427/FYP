<?php
require __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51R3yBQQZPLk7FzRY74j8818Wi2KEMJ4GeHDJAAGhxaEyeFBHEjLrTcY8uzB4v7kvd6yuKCgd4gQIqKArIhfNPIgy00Gktm1Et6');

header(header: 'Content-Type: application/json');

try {
  // 创建支付意图
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => 1000,
    'currency' => 'cny',
    'payment_method' => $payment_method_id,
    'automatic_payment_methods' => [
      'enabled' => true,
      'allow_redirects' => 'never' // 禁止重定向
    ],
    'confirm' => true,
  ]);

  // 插入数据库
  $pdo = new PDO(dsn: 'mysql:host=localhost;dbname=payments', username: 'root', password: '');
  $stmt = $pdo->prepare(query: "INSERT INTO orders (stripe_id, amount, status) VALUES (?, ?, ?)");
  $stmt->execute(params: [$paymentIntent->id, 1000, 'pending']);

  echo json_encode(value: ['success' => true]);

} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(response_code: 500);
  echo json_encode(value: ['error' => $e->getMessage()]);
}