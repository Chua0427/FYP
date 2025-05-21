<?php
// Restrict admin access to user pages
require_once __DIR__ . '/../app/restrict_admin.php';
?>
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
    <?php
    // Include CSRF protection
    require_once __DIR__ . '/../app/csrf.php';
    // Generate CSRF token and add it to meta tag
    $csrf_token = generateCsrfToken();
    ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
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
                $sql = "SELECT * FROM product p
                        WHERE p.status='New'
                        AND EXISTS (
                            SELECT 1 FROM stock s
                            WHERE s.product_id = p.product_id AND s.stock > 0
                            )
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
                $sql = "SELECT * FROM product p
                WHERE p.status='Promotion'
                AND EXISTS (
                    SELECT 1 FROM stock s
                    WHERE s.product_id = p.product_id AND s.stock > 0
                    )
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
                $sql = "SELECT * FROM product p
                WHERE p.status='Normal' AND p.product_categories='Jersey'
                AND EXISTS (
                    SELECT 1 FROM stock s
                    WHERE s.product_id = p.product_id AND s.stock > 0
                    )
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

        <h2 style="background-color: white; margin-top: 140px; margin-bottom: -60px;">Shop By Gender</h2>
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
        </div>

        <div class="informContainer">
            <div class="aboutUs">
                <img src="images/b787ff07-4fea-4f93-bc7c-1f10a1333ee8.webp" alt="">
                <h3>About Us</h3>
                <p>Founded in 2025, VEROSPORTS was born out of a passion for sports and a commitment to
                    innovation.......<a href="../fl/AboutVeroSports.php">read more </a></p>
            </div>
            <div class="aboutUs">
                <img src="images/NIK22275_1000_9__48380.jpg" alt="">
                <h3>Products Warranty</h3>
                <p>Founded in 2025, VEROSPORTS was born out of a passion for sports and a commitment to
                    innovation........<a href="../fl/warranty.php">read more </a></p>
            </div>
            <div class="aboutUs">
                <img src="images/66c063ce-e067-4f24-9ed7-713d6bcf766c.webp" alt="">
                <h3>Mission And Vision</h3>
                <p>At VEROSPORTS, our vision is to revolutionize the sports industry by offering top-tier products that
                    blend performance with style........<a href="../fl/MV.php">read more </a></p>
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
                
                // Get the button element that was clicked
                const button = event.target;
                const originalText = button.value;
                
                // First, fetch product sizes to handle products like shoes that require specific sizes
                fetch('../api/product_sizes.php?product_id=' + encodeURIComponent(productId))
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to get product sizes');
                    }
                    
                    // Default size determination logic
                    let productSize;
                    
                    if (data.sizes.length === 0) {
                        throw new Error('No sizes available for this product');
                    } else if (data.sizes.length === 1) {
                        // If only one size is available, use it
                        productSize = data.sizes[0].product_size;
                    } else if (data.product_type === 'Footwear') {
                        // Footwear typically has specific sizes - use the first available size
                        productSize = data.sizes[0].product_size;
                    } else if (data.product_type === 'Equipment' && data.sizes.some(s => s.product_size === 'One Size')) {
                        // If it's equipment and "One Size" is available, use it
                        productSize = 'One Size';
                    } else {
                        // For apparel, try to find a "M" (medium) size, or use the first available
                        const mediumSize = data.sizes.find(s => s.product_size === 'M');
                        productSize = mediumSize ? mediumSize.product_size : data.sizes[0].product_size;
                    }
                    
                    // Prepare form data
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('product_size', productSize);
                    formData.append('quantity', 1);
                    
                    // Add CSRF token
                    const csrfToken = getCsrfToken();
                    if (csrfToken) {
                        formData.append('csrf_token', csrfToken);
                    }
                    
                    // Show loading state
                    button.disabled = true;
                    button.value = 'Adding...';
                    
                    // Send AJAX request
                    return fetch('../api/add_to_cart.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Redirect to login if unauthorized
                            window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                            throw new Error('Please login to add items to your cart');
                        } else if (response.status === 403) {
                            // Handle CSRF token errors by refreshing the page
                            window.location.reload();
                            throw new Error('Session expired. Please try again.');
                        }
                    }
                    return response.json();
                })
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
                    showMessage(error.message || 'Error adding to cart. Please try again.', 'error');
                    console.error('Add to cart error:', error);
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.value = originalText;
                });
            }
            
            /**
             * Get CSRF token from the page or meta tag
             * @returns {string|null} CSRF token or null if not available
             */
            function getCsrfToken() {
                // Try to get token from hidden input
                const tokenInput = document.querySelector('input[name="csrf_token"]');
                if (tokenInput) {
                    return tokenInput.value;
                }
                
                // Try to get from meta tag
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (tokenMeta) {
                    return tokenMeta.getAttribute('content');
                }
                
                return null;
            }
            
            function showMessage(message, type) {
                // Create container div if it doesn't exist
                let container = document.querySelector('.message-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'message-container';
                    document.body.appendChild(container);
                    
                    // Style container to appear below the cart icon
                    container.style.position = 'fixed';
                    container.style.top = '70px';
                    container.style.right = '20px';
                    container.style.left = 'auto';
                    container.style.width = '300px';
                    container.style.zIndex = '999';
                    container.style.textAlign = 'center';
                }
                
                // Create message element
                const messageEl = document.createElement('div');
                messageEl.className = 'message ' + type;
                messageEl.textContent = message;
                
                // Style message
                messageEl.style.padding = '12px 20px';
                messageEl.style.marginBottom = '10px';
                messageEl.style.borderRadius = '4px';
                messageEl.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                messageEl.style.display = 'block';
                messageEl.style.width = '100%';
                messageEl.style.boxSizing = 'border-box';
                
                // Set colors based on message type
                if (type === 'success') {
                    messageEl.style.backgroundColor = '#28a745';
                    messageEl.style.color = 'white';
                } else if (type === 'error') {
                    messageEl.style.backgroundColor = '#dc3545';
                    messageEl.style.color = 'white';
                }
                
                // Add animation
                messageEl.style.animation = 'fadeIn 0.3s';
                
                // Add to container
                container.appendChild(messageEl);
                
                // Auto-remove after delay
                setTimeout(() => {
                    messageEl.style.animation = 'fadeOut 0.3s';
                    setTimeout(() => messageEl.remove(), 300);
                }, 3000);
            }
        });
        </script>
</body>

</html>