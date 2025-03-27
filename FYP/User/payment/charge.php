<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php'; // Stripe 密钥
require __DIR__ . '/app/init.php';
use Ramsey\Uuid\Uuid;

// 配置日志
ini_set(option: 'error_log', value: __DIR__ . '/logs/error.log');
error_reporting(error_level: E_ALL);
ini_set(option: 'display_errors', value: 0);

function log_message($level, $message): void {
    $log = date(format: "[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log(message: $log, message_type: 3, destination: __DIR__ . '/logs/payment.log');
}

header(header: 'Content-Type: application/json');

try {
    $pdo = new PDO(dsn: 'mysql:host=localhost;dbname=verosports', username: 'root', password: '');
    $pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    $user_id = 1; // 实际应从Session获取
    $total_price = 1000; // 实际应从购物车计算
    $shipping_address = '测试地址';

    // 生成唯一 UUID 作为订单 ID
    do {
        $order_id = Uuid::uuid4()->toString();
        $stmt = $pdo->prepare(query: "SELECT order_id FROM orders WHERE order_id = ?");
        $stmt->execute(params: [$order_id]);
    } while ($stmt->rowCount() > 0);

    // 插入订单
    $stmt = $pdo->prepare(query: "INSERT INTO orders (order_id, user_id, total_price, shipping_address) VALUES (?, ?, ?, ?)");
    $stmt->execute(params: [$order_id, $user_id, $total_price, $shipping_address]);

    // 获取支付方式
    $payment_method_id = $_POST['payment_method_id'] ?? null;
    if (!$payment_method_id) {
        throw new Exception(message: 'Invalid payment method ID');
    }

    // 创建 PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $total_price * 100,
        'currency' => 'MYR',
        'payment_method' => $payment_method_id,
        'metadata' => ['order_id' => $order_id, 'user_id' => $user_id],
        'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
        'confirm' => true,
    ]);

    // 记录支付信息
    $stmt = $pdo->prepare(query: "INSERT INTO payment (order_id, total_amount, payment_method, stripe_id) VALUES (?, ?, 'stripe', ?)");
    $stmt->execute(params: [$order_id, $total_price, $paymentIntent->id]);

    $pdo->commit();
    log_message(level: 'INFO', message: "Payment initiated for Order ID: $order_id | Stripe ID: {$paymentIntent->id}");

    echo json_encode(value: ['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    $pdo->rollBack();
    log_message(level: 'ERROR', message: "Charge error: " . $e->getMessage());
    http_response_code(response_code: 500);
    echo json_encode(value: ['error' => $e->getMessage()]);
}

?>