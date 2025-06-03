<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $product_id=$_GET['id'];

        $sql_delete = "UPDATE product SET deleted = 1 WHERE product_id = ?";

        $stmt_delete = $conn->prepare($sql_delete);

        $stmt_delete->bind_param("i", $product_id);

        if ($stmt_delete->execute()) {
            echo '<script>alert("Delete Successfully"); window.location.href="view_product.php"</script>';
        } else {
            echo "Error deleting product: " . $stmt_delete->error;
        }
    }
?>