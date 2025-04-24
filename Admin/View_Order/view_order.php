<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_order.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="order-container">
            <div class="order-table">
                <h3>View Order</h3>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th></th>
                        <th>Customer Name</th>
                        <th>Customer Phone Number</th>
                        <th>Total Price</th>
                        <th>Order Time</th>
                        <th>View Order Items</th>
                    </tr>

                    <?php
                        include __DIR__ . '/../../connect_db/config.php';

                        $sql= "SELECT o.*,u.first_name,u.last_name,u.mobile_number, u.profile_image FROM orders o
                               JOIN users u ON o.user_id= u.user_id";
                        $result= $conn->query($sql);

                        while($row= $result->fetch_assoc()){
                            $orderId = $row["order_id"];

                            echo '<tr>
                                    <td>'. $row["order_id"].'</td>
                                    <td><img src="../../upload/'.$row['profile_image'].'"</td>
                                    <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                                    <td>'.$row['mobile_number'].'</td>
                                    <td style="font-weight:bold;";>RM '. $row["total_price"].'</td>
                                    <td>'. $row["order_at"].'</td>
                                    <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="items">View</a></td>
                                </tr>';
                        }
                    ?>
                </table>

            </div>
        </div>
    </div>

</body>
</html>