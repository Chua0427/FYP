<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="promotion_product.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>

    <div class="promotion-billboard">
        <img src="images/flash-sale-realistic-promotion-banner-online-shop-template_16148-1493.png" alt="">
        <div class="slogan-contain">
            <div class="slogan">
                <p>VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORTS
                    VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORTS
                    :VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORTS
                    VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORT
                    VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORTS
                    VEROSPORTS:The Real Sports : Unleash Your True Potential : The Power of True Sports : Unleash Your
                    True
                    Potential : Where Passion Meets Performance : Elevate Your Game : Play Hard, Win Real : VEROSPORTS
                </p>
            </div>
    </div>

    <button id="filterbtn" onclick="openfilter()"><i class="fa-solid fa-filter"></i><span>Filter</span></button>

    <div class="container">
        <div class="sidebar">
            <div class="sidebar-container">
                <div class="filter1">
                    <h3>Price</h3>
                    <P>RM <input type="number" value="0" min="0" id="minprice"><span> - RM <input id="maxprice"type="number" value="0" min="0"></span></P>
                </div>
                <div class="filter">
                    <h3>Product Category</h3>
                    <h4>Footwear</h4>
                    <label><input type="checkbox" name="category" value="Boot"> Boot</label><br>
                    <label><input type="checkbox" name="category" value="Futsal"> Futsal</label><br>
                    <label><input type="checkbox" name="category" value="Running"> Running</label><br>
                    <label><input type="checkbox" name="category" value="Court"> Court</label><br>
                    <label><input type="checkbox" name="category" value="Training"> Training</label><br>
                    <label><input type="checkbox" name="category" value="School Shoes"> School Shoes</label><br>
                    <label><input type="checkbox" name="category" value="Kids Shoes"> Kids Shoes</label><br>
                    
                    <h4>Apparel</h4>
                    <label><input type="checkbox" name="category" value="Jerseys"> Jerseys</label><br>
                    <label><input type="checkbox" name="category" value="Jackets"> Jackets</label><br>
                    <label><input type="checkbox" name="category" value="Pants"> Pants</label><br>
                    <label><input type="checkbox" name="category" value="Leggings"> Leggings</label><br>

                    <h4>Equipment</h4>
                    <label><input type="checkbox" name="category" value="Bags"> Bags</label><br>
                    <label><input type="checkbox" name="category" value="Caps"> Caps</label><br>
                    <label><input type="checkbox" name="category" value="Football Accessories"> Football Accessories</label><br>
                    <label><input type="checkbox" name="category" value="Socks"> Socks</label><br>
                    <label><input type="checkbox" name="category" value="Gym Accessories"> Gym Accessories</label><br>

                </div>

                <div class="filter">
                    <h3>Gender</h3>
                    <label><input type="checkbox" name="gender" value="Men"> Men</label><br>
                    <label><input type="checkbox" name="gender" value="Women"> Women</label><br>
                    <label><input type="checkbox" name="gender" value="Kid"> Kid</label><br>
                </div>
                <div class="filter">
                    <h3>Shop By Brand</h3>
                    <label><input type="checkbox" name="brand" value="Nike"> Nike</label><br>
                    <label><input type="checkbox" name="brand" value="Adidas"> Adidas</label><br>
                    <label><input type="checkbox" name="brand" value="Puma"> Puma</label><br>
                    <label><input type="checkbox" name="brand" value="Umbro"> Umbro</label><br>
                    <label><input type="checkbox" name="brand" value="Lotto"> Lotto</label><br>
                    <label><input type="checkbox" name="brand" value="Asics"> Asics</label><br>
                    <label><input type="checkbox" name="brand" value="New Balance"> New Balance</label><br>
                    <label><input type="checkbox" name="brand" value="Under Armour"> Under Armour</label><br>
                </div>
            </div>
        </div>

        <div class="product-wrapper">
            <div class="product-container">

            <?php
                include __DIR__ . '/../../connect_db/config.php';

                $sql= "SELECT* FROM product WHERE status='Promotion'";
                $result= $conn->query($sql);

                $sql1 = "
                    SELECT p.*, s.stock
                    FROM product p
                    LEFT JOIN stock s ON p.product_id = s.product_id
                    ";

                $result1 = $conn->query($sql1);

                while($row = $result->fetch_assoc())
                {
                    $price = $row['price'];
                    $discount_price = $row['discount_price'];

                    $discountPercent = round((($price - $discount_price) / $price) * 100);
                        
                    $product_id = $row['product_id'];
                    $stock = 0;
                            
                    while ($row1 = $result1->fetch_assoc()) {
                        if ($row1['product_id'] == $product_id) {
                            $stock = $row1['stock'];
                            break;
                        }
                    }

                    if ($stock >0) {

                        echo'<div class="product-column">
                                <div class="discount">'.$discountPercent.'% OFF</div>
                                <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                                    <img src="../../upload/'.$row['product_img1'].'" alt="">
                                    <p class="product-name">'.$row['product_name'].'</p>
                                    <div class="price">
                                        <span class="promotionPrice">RM '.$row['discount_price'].'</span>
                                        <span class="discountPrice">RM '.$row['price'].'</span>
                                    </div>
                                </a>
                            </div>';
                    }
                }
            ?>
            
            </div>
        </div>
    </div>
        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?> 
        <script src="promotion_product.js"></script>
</body>

</html>