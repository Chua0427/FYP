<?php

$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'verosports'; 


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

include 'db.php';

// 示例：在 charge.php 处理支付记录
$payment_id = $_POST['payment_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$status = $_POST['status'] ?? null;

if ($payment_id && $amount && $status) {
    execute("INSERT INTO payments (payment_id, amount, status) VALUES (?, ?, ?)", [$payment_id, $amount, $status]);
    echo "Payment recorded successfully.";
} else {
    echo "Missing payment details.";
}


function query($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($params) {
        $types = str_repeat('s', count($params)); 
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}


function fetchAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchOne($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->get_result()->fetch_assoc();
}

function execute($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->affected_rows;
}

?>
