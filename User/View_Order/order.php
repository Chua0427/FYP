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
            margin: 0px auto 150px auto;
            padding: 0 20px;
            position:relative;
            top: 120px;
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
            margin: 20px 0;
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

        #button{
            position: relative;
            top: 100px;
            margin-left: 30px;
            padding: 10px 20px;
            color: black;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            background-color: lightgray;
        }

        #button i{
            color: black;
        }

        #button:hover{
            background-color: gray;
        }

    </style>
</head>
<body>

<?php 
    include __DIR__ . '/../Header_and_Footer/header.php'; 
?>
<button onclick="history.back()" id="button"><i class="fa-solid fa-arrow-left"></i>  Go Back</button>
<div class="container">
    <?php if($result1->num_rows>0): ?>
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
                <a href="../order/view_payment_details.php?order_id=<?php echo htmlspecialchars((string)$row['order_id']); ?>">View Payment Details</a>
                <a href="../Delivery_Status_Page/delivery.php?id=<?php echo $row['order_id'] ?>">View Status</a>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <h2 style="margin-bottom: 20px;">No Orders Now</h2>
                <a href="../HomePage/homePage.php" style="background: #ff5722; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Continue Shopping</a>
            </div>
        <?php endif; ?>
</div>

<?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

</body>
</html>
