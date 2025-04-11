<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="homePage.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>

    <div class="slideshow-container">
        <i class="fa fa-arrow-left" id="BillboardButton" aria-hidden="true"></i>

        <?php
            include __DIR__ . '/../../connect_db/config.php';

            $sql="SELECT * FROM billboard";
            $result=$conn->query($sql);

            while($row=$result->fetch_assoc()){
                echo '<div class="slide">
                    <img src="../../upload/'.$row['image'].'" alt="">
                </div>';
            }
        ?>
        <i class="fa fas fa-arrow-right" id="BillboardNextButton" aria-hidden="true"></i>
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
    </div>

    <div class="column1">
        <div class="column">
            <img src="images/all.jpg">
            <p>All Products</p>
            <input type="button" class="button" value="Buy Now" onclick="window.location.href='../All_Product_Page/all_product.php'">
        </div>
        <div class="column">
            <img src="images/Screenshot 2025-02-26 235034.png">
            <p>Running</p>
            <input type="button" class="button" value="Buy Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Running'">
        </div>
        <div class="column">
            <img src="images/football.png">
            <p>Football Boots</p>
            <input type="button" class="button" value="Buy Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Boot'">
        </div>
    </div>

    <div class="title">
        <h2>NEW ARRIVALS</h2>
        <p>"Don't miss out - it is new arrivals product!"</p>
    </div>

    <div class="NewArrivalsWrapper">
        <i class="fa fa-arrow-left" id="prevButton" aria-hidden="true"></i>
        <div class="NewArrivalsContainer">
            <div class="Newcolumn">
                <img class="img" src="images/new.png">
                <p><input type="button" class="button" value="Shop Now" onclick="window.location.href='../New_Arrival_Page/new_product.php'"></p>
            </div>
            <?php
                $sql="SELECT* FROM product WHERE status='New' ORDER BY RAND() LIMIT 5";
                $result= $conn->query($sql);

                while($row=$result->fetch_assoc())
                {
                    echo '<div class="Newcolumn">
                            <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                                <img src="../../upload/'.$row['product_img1'].'">
                                <p class="product-name">'.$row['product_name'].'</p>
                                <div class="price">
                                    <p class="NewArrivalPrice">RM '.$row['price'].'</p>
                                </div>
                            </a>
                            <input type="button" class="cartButton" value="Quick Add">
                        </div>';
                }
            ?>
        </div>
        <i class="fa fa-arrow-right" id="nextButton" aria-hidden="true"></i>
    </div>


    <div class="sub-billboard">
        <img src="images/Screenshot 2025-03-02 180412.png" alt="">
        <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Boot'">
    </div>

    <div class="promotionWrapper">
        <i class="fa fa-arrow-left" id="pButton" aria-hidden="true"></i>
        <div class="PromotionContainer">
            <div class="promotion" style="width: 300px; height: 350px;">
                <img src="images/promotion-img (2).jpg">
                <p><input type="button" class="button" value="Shop Now" onclick="window.location.href='../Promotion_Page/promotion_product.php'"></p>
            </div>

            <?php
                $sql="SELECT* FROM product WHERE status='Promotion' ORDER BY RAND() LIMIT 5";
                $result= $conn->query($sql);

                while($row= $result->fetch_assoc()){
                    $price = $row['price'];
                    $discount_price = $row['discount_price'];

                    $discountPercent = round((($price - $discount_price) / $price) * 100);

                    echo '<div class="promotion">
                            <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                                <div class="discount">'.$discountPercent.'% OFF</div>
                                <img src="../../upload/'.$row['product_img1'].'">
                                <p class="product-name">'.$row['product_name'].'</p>
                                <div class="price">
                                <span class="promotionPrice">RM '.$row['discount_price'].'</span>
                                <span class="discountPrice">RM '.$row['price'].'</span>
                                </div>  
                                <input type="button" class="cartButton" value="Quick Add">
                            </a>
                           </div>';
                }
            ?>
        </div>
        <i class="fa fa-arrow-right" id="pnextButton" aria-hidden="true"></i>
    </div>

    <div class="footballAndGym">
        <div class="picture">
            <img src="images/football-training-equipment-7-1024x664.jpg">
            <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Football Accessories'">
        </div>

        <div class="picture">
            <img src="images/istockphoto-1287874606-612x612.jpg">
            <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Gym Accessories'">
        </div>
    </div>
    <div class="jerseyheader">
        <img src="images/jersey.png">
        <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?product_categories=Jersey'">
    </div>
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
    </div>

    


    <div class="columnJersey">
        <i class="fa fa-arrow-left" id="jButton" aria-hidden="true"></i>
        <div class="JerseyContainer">
            <?php
                $sql="SELECT* FROM product WHERE product_categories='Jersey' && status='Normal' ORDER BY RAND() LIMIT 5";
                $result= $conn->query($sql);

                while($row= $result->fetch_assoc()){
                    echo '<div class="Jersey">
                        <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                            <img src="../../upload/'.$row['product_img1'].'">
                            <div class="product-name">'.$row['product_name'].'</div>
                            <div class="price">
                                <div class="jerseyPrice">RM '.$row['price'].'</div>
                            </div>
                            <input type="button" class="cartButton" value="Quick Add">
                        </a>
                    </div>';
                }
            ?> 
        </div>
        <i class="fa fa-arrow-right" id="jnextButton" aria-hidden="true"></i>
    </div>

    <div class="brandWrapper">
        <h2>TOP OF BRANDS</h2>
        <div class="brandContainer">
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Nike"><img src="images/nike.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Adidas"><img src="images/adidas.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Puma"><img src="images/puma.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Asics"><img src="images/asics.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Under Amour"><img src="images/underAmour.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=New Balance"><img src="images/newbalance.jpg"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Umbro"><img src="images/umbro.png"></a>
            </div>
            <div class="brand">
                <a id="nike" href="../All_Product_Page/all_product.php?brand=Lotto"><img src="images/Lotto.png"></a>
            </div>
        </div>

        <h2 style="background-color: white; margin-top: 100px; ">Shop By Gender</h2>
        <div class="column1" style="margin-top:1%;">
            <div class="column" style="width: 400px; height: 600px;">
                <img src="images/man.jpg">
                <p>Men</p>
                <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?gender=Men'">
            </div>

            <div class="column" style="width: 400px; height: 600px;">
                <img src="images/women.jpg">
                <p>Women</p>
                <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?gender=Women'">
            </div>
            <div class="column" style="width: 400px; height: 600px;">
                <img src="images/kids.jpg">
                <p>Kid</p>
                <input type="button" class="button" value="Shop Now" onclick="window.location.href='../All_Product_Page/all_product.php?gender=Kid'">
            </div>
        </div>

        <div class="usWrapper">
            <h3>Why Choose Us?</h3>
            <div class="container">
                <div class="warranty">
                    <img src="images/warranty.webp">
                    <p>1 year waranty for products.</p>
                </div>
                <div class="payment">
                    <img src="images/Visa-Logo-2006.png">
                    <p>Secure payment methods.</p>
                </div>
                <div class="delivery">
                    <img src="images/fast delivery.avif">
                    <p>Fast deliver < 2 days.</p>
                </div>
                <div class="respon">
                    <img src="images/respons.png">
                    <p>Support when have problems.</p>
                </div>
            </div>
            <h3></h3>
        </div>

        <div class="informContainer">
            <div class="aboutUs">
                <img src="images/b787ff07-4fea-4f93-bc7c-1f10a1333ee8.webp" alt="">
                <h3>About Us</h3>
                <p>Founded in 2025, VEROSPORTS was born out of a passion for sports and a commitment to
                    innovation.......<a href="#">read more </a></p>
            </div>
            <div class="aboutUs">
                <img src="images/NIK22275_1000_9__48380.jpg" alt="">
                <h3>Products Warranty</h3>
                <p>Founded in 2025, VEROSPORTS was born out of a passion for sports and a commitment to
                    innovation........<a href="#">read more </a></p>
            </div>
            <div class="aboutUs">
                <img src="images/66c063ce-e067-4f24-9ed7-713d6bcf766c.webp" alt="">
                <h3>Mission And Vision</h3>
                <p>At VEROSPORTS, our vision is to revolutionize the sports industry by offering top-tier products that
                    blend performance with style........<a href="#">read more </a></p>
            </div>
        </div>


        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?>
        <script src="homePage.js"></script>
</body>

</html>