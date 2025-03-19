<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="all_product.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>

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
                <div class="product-column">
                    <a href="../ProductPage/product.php">
                        <img src="images/nike_image.png" alt="">
                        <p>Nike</p>
                        <p>RM <span class="price">199.99</span></p>
                    </a>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
                <div class="product-column">
                    <img src="images/nike_image.png" alt="">
                    <p>Nike</p>
                    <p>RM <span class="price">199.99</span></p>
                </div>
            </div>
        </div>
    </div>
        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?> 
        <script src="all_product.js"></script>
</body>

</html>