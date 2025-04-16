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
    <?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    include __DIR__ . '/../Header_and_Footer/header.php'; 
    ?>

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

    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['first_name']) && !isset($_SESSION['welcome_shown'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = "Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!\n\nCheck out our latest products and exclusive deals just for you.<?php if (isset($_SESSION['last_visit'])): ?>\n\nYour last visit was on <?php echo date('F j, Y, g:i a', $_SESSION['last_visit']); ?><?php endif; ?>";
            alert(welcomeMessage);
        });
    </script>
    <?php 
    // Set flag that welcome message has been shown
    $_SESSION['welcome_shown'] = true;
    
    // Update last visit timestamp
    $_SESSION['last_visit'] = time();
    endif; 
    ?>

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
                $sql="SELECT p.*, SUM(s.stock) as total_stock
                    FROM product p
                    JOIN stock s ON p.product_id = s.product_id
                    WHERE p.status='New' 
                    GROUP BY p.product_id
                    HAVING total_stock > 0
                    ORDER BY RAND()
                    LIMIT 5";
                    
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
                            <input type="button" class="quick-add-button cartButton" value="Quick Add" data-product-id="'.$row['product_id'].'">
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
                $sql="SELECT p.*, SUM(s.stock) as total_stock
                FROM product p
                JOIN stock s ON p.product_id = s.product_id
                WHERE p.status='Promotion'
                GROUP BY p.product_id
                HAVING total_stock > 0
                ORDER BY RAND()
                LIMIT 5";
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
                            </a>
                            <input type="button" class="quick-add-button cartButton" value="Quick Add" data-product-id="'.$row['product_id'].'">
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
                $sql="SELECT p.*, SUM(s.stock) as total_stock
                    FROM product p
                    JOIN stock s ON p.product_id = s.product_id
                    WHERE p.status='Normal' && product_categories='Jersey'
                    GROUP BY p.product_id
                    HAVING total_stock > 0
                    ORDER BY RAND()
                    LIMIT 6";
                $result= $conn->query($sql);

                while($row= $result->fetch_assoc()){
                    echo '<div class="Jersey">
                        <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                            <img src="../../upload/'.$row['product_img1'].'">
                            <div class="product-name">'.$row['product_name'].'</div>
                            <div class="price">
                                <div class="jerseyPrice">RM '.$row['price'].'</div>
                            </div>
                        </a>
                        <input type="button" class="quick-add-button cartButton" value="Quick Add" data-product-id="'.$row['product_id'].'">
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


        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
        <script src="homePage.js"></script>

        <style>
        .welcome-banner {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .welcome-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px;
        }
        
        .welcome-content h2 {
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .welcome-content p {
            margin-bottom: 0;
            font-size: 16px;
        }
        
        .last-visit {
            font-size: 14px !important;
            opacity: 0.8;
            margin-top: 5px !important;
        }
        
        .quick-add-button {
            cursor: pointer;
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle quick add buttons
            const quickAddButtons = document.querySelectorAll('.quick-add-button');
            
            quickAddButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-product-id');
                    quickAddToCart(productId);
                });
            });
            
            function quickAddToCart(productId) {
                // Check if user is logged in
                <?php if(!isset($_SESSION['user_id'])): ?>
                    window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                    return;
                <?php endif; ?>
                
                // Default size (first available)
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('product_size', 'M'); // Default size
                formData.append('quantity', 1);
                
                // Show loading state
                const button = event.target;
                const originalText = button.value;
                button.disabled = true;
                button.value = 'Adding...';
                
                // Send AJAX request
                fetch('/FYP/User/api/add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        
                        // Update cart counter
                        const cartCounter = document.getElementById('cartCount');
                        if (cartCounter && data.cart_count) {
                            cartCounter.textContent = data.cart_count;
                            cartCounter.style.display = 'block';
                        }
                    } else {
                        showMessage(data.error || 'Failed to add item to cart', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error adding to cart. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.value = originalText;
                });
            }
            
            function showMessage(message, type) {
                // Check if a message container already exists
                let messageContainer = document.querySelector('.message-container');
                
                // If not, create one
                if (!messageContainer) {
                    messageContainer = document.createElement('div');
                    messageContainer.className = 'message-container';
                    document.body.appendChild(messageContainer);
                    
                    // Style the container
                    messageContainer.style.position = 'fixed';
                    messageContainer.style.top = '20px';
                    messageContainer.style.right = '20px';
                    messageContainer.style.zIndex = '1000';
                }
                
                // Create message element
                const messageElement = document.createElement('div');
                messageElement.className = `message ${type}`;
                messageElement.innerHTML = message;
                
                // Style the message
                messageElement.style.padding = '12px 20px';
                messageElement.style.marginBottom = '10px';
                messageElement.style.borderRadius = '4px';
                messageElement.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                
                if (type === 'success') {
                    messageElement.style.backgroundColor = '#28a745';
                    messageElement.style.color = 'white';
                } else if (type === 'error') {
                    messageElement.style.backgroundColor = '#dc3545';
                    messageElement.style.color = 'white';
                }
                
                // Add to container
                messageContainer.appendChild(messageElement);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    messageElement.style.opacity = '0';
                    messageElement.style.transition = 'opacity 0.3s ease-out';
                    setTimeout(() => {
                        messageElement.remove();
                    }, 300);
                }, 3000);
            }
        });
        </script>
</body>

</html>