<?php
require __DIR__ . '/../../../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51R3yBQQZPLk7FzRY74j8818Wi2KEMJ4GeHDJAAGhxaEyeFBHEjLrTcY8uzB4v7kvd6yuKCgd4gQIqKArIhfNPIgy00Gktm1Et6');

header('Content-Type: application/json');

try {
  // 创建支付意图
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => 1000, // 金额以分单位
    'currency' => 'cny',
    'payment_method' => $_POST['payment_method_id'],
    'confirmation_method' => 'manual',
    'confirm' => true,
  ]);

  // 插入数据库
  $pdo = new PDO('mysql:host=localhost;dbname=payments', 'root', '');
  $stmt = $pdo->prepare("INSERT INTO orders (stripe_id, amount, status) VALUES (?, ?, ?)");
  $stmt->execute([$paymentIntent->id, 1000, 'pending']);

  echo json_encode(['success' => true]);

} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}