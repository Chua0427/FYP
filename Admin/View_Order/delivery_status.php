<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    include __DIR__ . '/../../connect_db/config.php';
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET delivery_status=? WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order #" . $order_id . " updated to $status'); window.location.href='view_order.php';</script>";
    } else {
        echo "<script>alert('Failed to update order');</script>";
    }
}
?>
