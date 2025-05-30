<?php
declare(strict_types=1);

include __DIR__ . '/../../connect_db/config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Only show orders that are not delivered
$sql = "SELECT * FROM orders WHERE user_id = ? AND delivery_status != 'delivered' ORDER BY order_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result1 = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports - Pending Orders</title>
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
            position: relative;
            top: 100px;
            margin-bottom: 150px;
            padding: 0 20px;
            margin-left: auto;
            margin-right: auto;
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
        
        .no-orders {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }
        
        .no-orders i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php 
    include __DIR__ . '/../Header_and_Footer/header.php'; 
?>

<div class="container">
    <h2 style="text-align: center; margin-bottom: 20px;">Pending Orders</h2>

    <?php if ($result1->num_rows > 0): ?>
        <?php while($row = $result1->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>Order ID: <?php echo htmlspecialchars((string)$row['order_id']); ?></div>
                    <div>Status: <strong><?php echo htmlspecialchars($row['delivery_status']); ?></strong></div>
                </div>

                <?php
                    $order_id = (int)$row['order_id'];
                    // Use prepared statement for items query
                    $items_sql = "SELECT oi.*, p.product_name, p.product_img1 
                                 FROM order_items oi 
                                 JOIN product p ON oi.product_id = p.product_id 
                                 WHERE oi.order_id = ?";
                    $items_stmt = $conn->prepare($items_sql);
                    $items_stmt->bind_param("i", $order_id);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();
                    
                    while ($item = $items_result->fetch_assoc()):
                ?>
                <div class="order-body">
                    <div class="order-image">
                        <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" alt="product">
                    </div>

                    <div class="order-info">
                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                        <p>Quantity: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                        <p>Price: RM <?php echo number_format((float)$item['price'], 2); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
                <div class="order-footer">
                    <p>Order Time: <?php echo date("Y-m-d H:i", strtotime($row['order_at'])); ?></p>
                    <p>Total: <strong>RM <?php echo number_format((float)$row['total_price'], 2); ?></strong></p>
                    <a href="../Delivery_Status_Page/delivery.php?id=<?php echo htmlspecialchars((string)$row['order_id']); ?>">View Status</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-orders">
            <i class="fas fa-shopping-bag"></i>
            <p>You don't have any pending orders.</p>
            <a href="../All_Product_Page/all_product.php" style="background: #ff5722; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; display: inline-block; margin-top: 10px;">Shop Now</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

</body>
</html>
