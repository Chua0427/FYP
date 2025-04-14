<?php
    include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['stock_id']) && isset($_GET['product_id'])){
        $stock_id = $_GET['stock_id'];
        $product_id = $_GET['product_id'];

        $sql="DELETE FROM stock WHERE stock_id = $stock_id";
        $result = $conn->query($sql);

        if($result) {
            echo "<script>alert('Stock delete successfully!'); window.location.href='view_stock.php?id=$product_id';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
?>