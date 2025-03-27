<?php
require 'db.php';

// 设置 JSON 响应头
header('Content-Type: application/json');

try {
    // 输入校验
    if (empty($_POST['payment_id']) || empty($_POST['amount']) || empty($_POST['status'])) {
        throw new Exception("Missing required fields");
    }

    $payment_id = $_POST['payment_id'];
    $amount = (float) $_POST['amount'];
    $status = $_POST['status'];

    // 验证支付状态
    $valid_status = ['pending', 'completed', 'failed', 'refunded'];
    if (!in_array($status, $valid_status)) {
        throw new Exception("Invalid status value");
    }

    // 连接数据库并插入数据
    $db = new Database();
    $result = $db->execute(
        "INSERT INTO payment (payment_id, total_amount, payment_status) VALUES (?, ?, ?)",
        [$payment_id, $amount, $status]
    );

    if ($result === false) {
        throw new Exception("Failed to save payment");
    }

    // 关闭数据库连接
    $db->close();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?>
