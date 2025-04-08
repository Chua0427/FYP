<?php
require 'db.php';


header('Content-Type: application/json');

try {

    if (empty($_POST['payment_id']) || empty($_POST['amount']) || empty($_POST['status'])) {
        throw new Exception("Missing required fields");
    }

    $payment_id = $_POST['payment_id'];
    $amount = (float) $_POST['amount'];
    $status = $_POST['status'];


    $valid_status = ['pending', 'completed', 'failed', 'refunded'];
    if (!in_array($status, $valid_status)) {
        throw new Exception("Invalid status value");
    }


    $db = new Database();
    $result = $db->execute(
        "INSERT INTO payment (payment_id, total_amount, payment_status) VALUES (?, ?, ?)",
        [$payment_id, $amount, $status]
    );

    if ($result === false) {
        throw new Exception("Failed to save payment");
    }


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
