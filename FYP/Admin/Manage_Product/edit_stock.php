<?php
include __DIR__ . '/../../connect_db/config.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['stock_id'])) {
        $stock_id = $_GET['stock_id'];

        $stock = ($_POST['stock']);

        $sql = "UPDATE stock SET stock = $stock, last_update_at = NOW() WHERE stock_id = $stock_id";
        $result= $conn->query($sql);
        
        if($result) {
            echo "<script>alert('Stock updated successfully!'); window.history.go(-2);</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_stock.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">

        <div class="editSize">
                <form action="" method="post">
                    <h3>Edit Stock</h3>
                    <input type="hidden" name="stock_id" value="<?php echo $stock['stock_id']; ?>">
                    <div class="column">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" value="<?php echo $stock['stock']; ?>" required>
                    </div>
                    <input type="hidden" name="edit_stock" value="1">
                    <button type="submit">Update Stock</button>
                </form>
            </div>
</div>
</body>
</html>