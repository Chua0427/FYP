<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php'; // Stripe 密钥
require __DIR__ . '/../app/init.php';
use Ramsey\Uuid\Uuid;

session_start();

// 确保 logs 目录存在
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// 配置日志
ini_set('error_log', __DIR__ . '/logs/error.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

function log_message($level, $message): void {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/payment.log');
}

header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    // 从 Session 获取 user_id
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        throw new Exception('User is not logged in');
    }

    // 检查 user_id 是否存在于 users 表
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Invalid user ID: $user_id");
    }

    // 订单信息
    $total_price = 1000; // 实际应从购物车计算
    $shipping_address = '测试地址';

    // 生成唯一 UUID 作为订单 ID
    do {
        $order_id = Uuid::uuid4()->toString();
        $stmt = $pdo->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
    } while ($stmt->rowCount() > 0);

    // 插入订单
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, total_price, shipping_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $user_id, $total_price, $shipping_address]);

    // 获取支付方式
    $payment_method_id = $_POST['payment_method_id'] ?? null;
    if (!$payment_method_id) {
        throw new Exception('Invalid payment method ID');
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
    $stmt = $pdo->prepare("INSERT INTO payment (order_id, total_amount, payment_method, stripe_id) VALUES (?, ?, 'stripe', ?)");
    $stmt->execute([$order_id, $total_price, $paymentIntent->id]);

    $pdo->commit();
    log_message('INFO', "Payment initiated for Order ID: $order_id | Stripe ID: {$paymentIntent->id}");

    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_message('ERROR', "Charge error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
