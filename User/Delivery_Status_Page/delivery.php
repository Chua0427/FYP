<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="delivery.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    include __DIR__ . '/../Header_and_Footer/header.php'; 

    include __DIR__ . '/../../connect_db/config.php';

    $order_id=17; //$_GET['id'];

    $sql='SELECT o.*, u.first_name, u.last_name, u.mobile_number, u.email FROM orders o JOIN users u ON o.user_id=u.user_id WHERE o.order_id= '.$order_id.'';
    $result= $conn->query($sql);
    $row= $result->fetch_assoc();

    $itemSql = "SELECT oi.quantity, oi.price, oi.product_size, p.product_name,p.product_img1
                FROM order_items oi
                JOIN product p ON oi.product_id = p.product_id
                WHERE oi.order_id = $order_id";
    $itemResult = $conn->query($itemSql);
    
    ?>

    <h1>Tracking Delivery</h1><p></p>


        <div class="delivery-status">
            <div class="step" id="preparing">
                <div class="circle"><i class="fa-solid fa-store"></i></div>
                <p>Preparing Your Order</p>
            </div>
            <div class="step" id="packing">
                <div class="circle"><i class="fa-solid fa-box"></i></div>
                <p>Packing Your Order</p>
            </div>
            <div class="step" id="assign">
                <div class="circle"><i class="fa-solid fa-truck"></i></div>
                <p>Assigning Our Driver</p>
            </div>
            <div class="step" id="shipping">
                <div class="circle"><i class="fa-solid fa-truck-fast"></i></div>
                <p>Order Is On The Way</p>
            </div>
            <div class="step" id="delivered">
                <div class="circle"><i class="fa-solid fa-location-dot"></i></div>
                <p>Delivered</p>
            </div>
        </div>

        <div class="invoice">
            <h2>Order</h2>
            <p style="margin-top: 20px;"><strong>Order ID: </strong><?php echo $row['order_id'] ?></p>
            <p><strong>Date: </strong><?php echo date("d/m/Y", strtotime($row['order_at'])); ?></p>
            <p><strong>Customer:</strong> <?php echo $row['first_name'] . " " . $row['last_name'] ?></p>
            <p><strong>Phone Number:</strong> <?php echo $row['mobile_number'] ?></p>
            <p><strong>Email:</strong> <?php echo $row['email'] ?></p>
            <p><strong>Shipping Address:</strong> <?php echo $row['shipping_address'] ?></p>
            
            <h3 >Order Items</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Product Name</th>
                    <th>Product Size</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>

                <?php
                    $total=0;
                    while($row1=$itemResult->fetch_assoc()){
                        $subtotal= $row1['quantity'] * $row1['price'];
                        $total += $subtotal;
                        echo '<tr>
                            <td><img src="../../upload/'.$row1["product_img1"].'"</td>
                            <td>'.$row1["product_name"].'</td>
                            <td>'.$row1["product_size"].'</td>
                            <td>'.$row1["quantity"].'</td>
                            <td style="color: red; font-weight: bold;">RM '.number_format($subtotal,2).'</td>
                        </tr>';
                    }
                ?>
                
            </table>

            <p style="margin-bottom: 20px; font-weight: bold; font-size:20px;"><strong>Total Price:</strong> RM <?php echo number_format($total,2) ?></p>
            <hr>
            <p style="margin-top:10px; margin-bottom: 10px;">Note: </p>
            <p>If you have any problem about delivery, please email to our customer service (Email:support@verosports.com)</p>
        </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    
            let currentStatus = "<?php echo $row['delivery_status']; ?>"; 
            
            let steps = document.querySelectorAll('.step');
            
            const statusOrder = ["prepare", "packing", "assign", "shipping", "delivered"];
            
            let currentIndex = statusOrder.indexOf(currentStatus);
            
            steps.forEach((step, index) => {
                if (index <= currentIndex) {
                    step.classList.add('completed');
                }
            });
        });
</script>

    
</body>

</html>