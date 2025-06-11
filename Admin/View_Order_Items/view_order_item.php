<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin (user_type = 2 or user_type = 3)
    if ($_SESSION['user_type'] != 2 && $_SESSION['user_type'] != 3) {
        // Redirect non-admin users to the main site
        header("Location: /FYP/User/HomePage/homePage.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_order_item.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

    <?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="items-table">
            <h3>Order Items</h3>
            <table>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Product Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php
                    if(isset($_GET['order_id'])){
                        $orderID=$_GET['order_id'];
                        $subtotal=0;
                        $total=0;

                        $sql= "SELECT o.*, p.product_img1, p.product_name FROM order_items o JOIN product p ON o.product_id=p.product_id WHERE o.order_id=$orderID";
                        $result = $conn->query($sql);
                    }
                    
                    while($row= $result->fetch_assoc()){
                        $subtotal=$row['quantity'] * $row['price'];
                        $total +=$subtotal;
                        echo '<tr>
                                <td><img src="../../upload/'.$row['product_img1'].'"></td>
                                <td>'.$row['product_name'].'</td>
                                <td>'.$row['product_size'].'</td>
                                <td>'.$row['quantity'].'</td>
                                <td style="color: red; font-weight: bold;">RM '.$row['price'].'</td>
                                <td style="color: black; font-weight: bold;"><strong>RM '.number_format($subtotal,2).'</strong></td>
                            </tr>';
                    }
                    ?>
                
            </table>
            <div class="total-wrapper">
            <p><strong>Total: RM <?php echo number_format($total, 2); ?></strong></p>
        </div>
        </div>
        
    </div>
    
</body>
</html>