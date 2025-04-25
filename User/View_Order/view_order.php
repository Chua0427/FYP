<?php
include __DIR__ . '/../../connect_db/config.php';
session_start();
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = $user_id AND delivery_status!= 'Delivered' ORDER BY order_at DESC";
$result1 = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            margin: 0;
            background: #f8f8f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .order-card {
            background: #fff;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            background:rgb(240, 240, 240);
            padding: 10px 15px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
        }

        .order-body {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid lightgray;
        }

        .order-image img {
            width: 80px;
            height: 80px;
            background: #eee;
            border-radius: 4px;
            margin-right: 15px;
        }

        .order-info {
            flex: 1;
        }

        .order-info h4 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .order-info p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #888;
        }

        .order-footer {
            text-align: right;
            padding: 10px 15px;
            border-top: 1px solid #eee;
        }

        .order-footer p {
            margin: 10px 0;
            font-size: 14px;
        }

        .order-footer a {
            background: #ff5722;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 10px;
            font-size: 15px;
        }

        .order-footer a:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>

<?php 
    include __DIR__ . '/../Header_and_Footer/header.php'; 
?>

<div class="container">
<?php while($row = $result1->fetch_assoc()): ?>
    <div class="order-card">
        <div class="order-header">
            <div>Order ID : <?php echo $row['order_id'] ?></div>
            <div>Status : <strong><?php echo  $row['delivery_status'] ?></strong></div>
        </div>

        <?php
            $order_id = $row['order_id'];
            $items_result = $conn->query("SELECT oi.*, p.product_name, p.product_img1 FROM order_items oi JOIN product p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");
            while ($item = $items_result->fetch_assoc()):
        ?>
        <div class="order-body">
            <div class="order-image">
                <img src="../../upload/<?php echo $item['product_img1'] ?>" alt="product">
            </div>

            <div class="order-info">
                <h4><?php echo $item['product_name'] ?></h4>
                <p>Quantity: <?php echo $item['quantity'] ?></p>
                <p>Price: RM <?php echo number_format($item['price'], 2) ?></p>
            </div>
        </div>
        <?php endwhile; ?>
        <div class="order-footer">
            <p>Order Time : <?php echo date("Y-m-d H:i", strtotime($row['order_at'])) ?></p>
            <p>Total : <strong>RM <?php echo number_format($row['total_price'], 2) ?></strong></p>
            <a href="../Delivery_Status_Page/delivery.php?id=<?php echo $row['order_id'] ?>">View Status</a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

</body>
</html>
