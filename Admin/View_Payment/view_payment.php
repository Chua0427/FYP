<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_payment.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

    <?php include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="items-table">
            <h3>View Payment Status</h3>
            <table>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>View Order Items</th>
                    <th>Total Amount</th>
                    <th>Payment Time</th>
                    <th>Payment Status</th>
                </tr>
                
                <?php
                    $sql= "SELECT* FROM payment";
                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '
                            <tr>
                                <td>'.$row['payment_id'].'</td>
                                <td>'.$row['order_id'].'</td>
                                <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="view_item">View</a></td>
                                <td style="font-weight: bold;"> RM '.$row['total_amount'].'</td>
                                <td>'.$row['payment_at'].'</td>
                                <td><span style="background-color:rgb(35, 161, 31); color: white; padding:10px; border-radius:10px; font-weight: bold;">'.$row['payment_status'].'</span></td>
                            </tr>';
                    }
                    ?>
                </a>
            </table>
                
        </div>

        
    </div>
</body>
</html>