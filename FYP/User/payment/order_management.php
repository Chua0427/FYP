<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require __DIR__ . '/../app/init.php';

session_start();

// 日志函数
function log_message($level, $message) {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/order_management.log');
}

// 连接数据库
try {
    $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    log_message('ERROR', "数据库连接失败: " . $e->getMessage());
    die(json_encode(['error' => '数据库连接失败']));
}

// 处理 API 请求
$action = $_GET['action'] ?? null;
header('Content-Type: application/json');

try {
    switch ($action) {
        // 获取订单详情
        case 'get_order_details':
            if (!isset($_GET['order_id'])) {
                throw new Exception("缺少订单 ID");
            }
            $order_id = $_GET['order_id'];
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("订单不存在: $order_id");
            }

            // 获取支付状态
            $stmt = $pdo->prepare("SELECT * FROM payment WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'order' => $order, 'payment' => $payment]);
            log_message('INFO', "获取订单详情: $order_id");
            break;

        // 更新订单状态
        case 'update_order_status':
            if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
                throw new Exception("缺少参数: order_id 或 status");
            }
            $order_id = $_POST['order_id'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("UPDATE orders SET delivery_status = ? WHERE order_id = ?");
            $stmt->execute([$status, $order_id]);

            echo json_encode(['success' => true, 'message' => "订单状态更新为 $status"]);
            log_message('INFO', "订单 $order_id 状态更新为 $status");
            break;

        // 获取所有订单
        case 'get_all_orders':
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'orders' => $orders]);
            break;

        // 处理错误请求
        default:
            throw new Exception("无效的 action 参数");
    }
} catch (Exception $e) {
    log_message('ERROR', "API 处理错误: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
