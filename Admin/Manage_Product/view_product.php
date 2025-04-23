<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_product.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="product-container">
            <div class="product-table">
                <h3>View Product</h3>
                <table>
                    <tr>
                        <th>Product ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Price (RM)</th>
                        <th></th>
                    </tr>

                    <?php
                        include __DIR__ . '/../../connect_db/config.php';

                        $sql= "SELECT * FROM product";
                        $result= $conn->query($sql);

                        while($row= $result->fetch_assoc()){
                            $finalPrice = $row["discount_price"] > 0 ? $row["discount_price"] : $row["price"];
                            echo '<tr>
                                    <td>'. $row["product_id"].'</td>
                                    <td><img src="../../upload/'. $row["product_img1"].'"</td>
                                    <td>'. $row["product_name"].'</td>
                                    <td style="color:orangered; font-weight:bold;">'. $row["brand"].'</td>
                                    <td>'. $row["product_categories"].'</td>
                                    <td>'. $row["gender"].'</td>
                                    <td>'. $row["status"].'</td>
                                    <td style="color:red; font-weight:bold;">'. number_format($finalPrice,2).'</td>
                                    <td><div class="button"><a href="view_stock.php?id='.$row["product_id"].'" class="stock-button"><i class="fa-solid fa-boxes-packing"></i></a>
                                    <a href="edit.php?id='.$row["product_id"].'" id="edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="delete.php?id='.$row["product_id"].'" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a></div></td>
                                </tr>';
                        }
                    ?>
                </table>
                    <div class="add-btn">
                        <a href="product.php" id="add"><i class="fa-solid fa-plus" style="margin: 5px;"></i>Add More</a>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>