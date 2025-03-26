<?php #card-element// charge.php 完整代码
require __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51R3yBQQZPLk7FzRY74j8818Wi2KEMJ4GeHDJAAGhxaEyeFBHEjLrTcY8uzB4v7kvd6yuKCgd4gQIqKArIhfNPIgy00Gktm1Et6');

header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->beginTransaction();

    // 1. 创建订单
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, total_price, shipping_address) 
        VALUES (?, ?, ?)
    ");
    $user_id = 1; // 实际应从Session获取
    $total_price = 1000; // 实际应从购物车计算
    $shipping_address = '测试地址';
    $stmt->execute([$user_id, $total_price, $shipping_address]);
    $order_id = $pdo->lastInsertId();

    // 2. 创建支付记录
    $stmt = $pdo->prepare("
        INSERT INTO payment 
        (order_id, total_amount, payment_method, stripe_id) 
        VALUES (?, ?, 'stripe', ?)
    ");
    $payment_method_id = $_POST['payment_method_id'];
    
    // 3. 创建Stripe PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $total_price,
        'currency' => 'cny',
        'payment_method' => $payment_method_id,
        'metadata' => [
            'order_id' => $order_id,
            'user_id' => $user_id
        ],
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never'
        ],
        'confirm' => true,
    ]);

    // 更新支付记录
    $stmt->execute([$order_id, $total_price, $paymentIntent->id]);
    $pdo->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (\Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}