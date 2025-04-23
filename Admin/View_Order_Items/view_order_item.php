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

    <?php include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="items-table">
            <h3>View Oders Items</h3>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Product Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                <?php
                    $sql= "SELECT o.*, p.product_img1, p.product_name FROM order_items o JOIN product p WHERE o.product_id=p.product_id";
                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '<tr>
                                <td>'.$row['order_id'].'</td>
                                <td><img src="../../upload/'.$row['product_img1'].'"</td>
                                <td>'.$row['product_name'].'</td>
                                <td>'.$row['product_size'].'</td>
                                <td>'.$row['quantity'].'</td>
                                <td style="color: red; font-weight: bold;">RM '.$row['price'].'</td>
                            </tr>';
                    }
                    ?>
                
            </table>
                
        </div>

        
    </div>
</body>
</html>