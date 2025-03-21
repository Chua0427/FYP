<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
        <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>

        <div class="productContainer">
        
            <div class="imgContainer">
                <i class="fa fa-arrow-left" id="productButton"></i>
                <img id="main_image" src="images/nike_image.png">
                <i class="fa fa-arrow-right" id="productnextButton"></i>

                <div class="small-img-group">
                    <div class="small-column">
                        <img class="small-img" src="images/nike2.png">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="images/nike3.png">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="images/nike4.png">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="images/nike_image.png">
                    </div>
                </div>
            </div>
            
            <div class="productDetails">
                <h1 id="brand">Nike</h1>
                <h3 id="name">Nike Phantom GX 2</h3>
                <span>SKU :</span><span id="sku">12345678</span><p style="padding-top: 20px;"></p>
                <span style="color: #e60000; font-size: 20px; font-weight: bold;">RM </span><span id="price">99.99</span> 
                <span class="size-chart-icon" onclick="openModal()"><i class="fa-solid fa-ruler-combined"></i></span>

                <div class="sizeContainer">
                    <label style="margin-top: 8px;">Size:</label>
                    <select id="size">
                        <option>US 7</option>
                        <option>US 8</option>
                        <option>US 9</option>
                        <option>US 10</option>
                    </select>
                
                    <input type="number" id="quantity" value="1" min="1">
                </div>

                <button class="add-to-cart">Add to Cart</button>
                
                <div class="tabContainer">
                    <div class="tab">
                        <button class="tab-button active" onclick="openTab(event, 'product-info')">Product Info</button>
                        <button class="tab-button" onclick="openTab(event, 'review')">Review</button>
                        <button class="tab-button" onclick="openTab(event, 'delivery')">Delivery</button>
                        <button class="tab-button" onclick="openTab(event, 'returns')">Returns</button>
                    </div>
                </div>
                
                <div class="tab-content active" id="product-info">
                    <p>Nike Phantom GX 2 Elite LV8 FG Low-Top Football Boots Obsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you’ve calmly found the final third of the field, thanks to your uncanny mix of on-ball guile and grace. Go finish the job in the Phantom GX 2 Elite. Revolutionary Nike Gripknit covers the striking area of the cleat while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite Boots for you and the world’s biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear. </p>
                </div>

                <div class="tab-content" id="review">
                    <p>Customer reviews will appear here.</p>
                </div>

                <div class="tab-content" id="delivery">
                    <p>Standard Delivery: 2-3 business days for West Malaysia and 2-5 business days for East Malaysia respectively.</p>
                    <p>Free Delivery Sitewide</p>
                </div>

                <div class="tab-content" id="returns">
                    <p>Return policy and refund details.</p>
                </div>
            </div>
        </div>

        <div class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h3>Size Chart</h3>
                <img id="size-chart-img" src="images/size_chart.jpg" alt="Size Chart" class="size-chart-img">
            </div>
        </div>

        <div class="recomend-container">
            <h1>Recomended</h1>
            <div class="recomend-column">
                <div class="recomend-img">
                    <img src="images/nike_image.png" alt="">
                    <p class="Name">Nike</p>
                    <p class="Price">RM 100</p>
                    <input type="button" class="cartButton" value="Quick Add">
                </div>
                
                <div class="recomend-img">
                    <img src="images/nike_image.png" alt="">
                    <p class="Name">Nike</p>
                    <p class="Price">RM 100</p>
                    <input type="button" class="cartButton" value="Quick Add">
                </div>
                
                <div class="recomend-img">
                    <img src="images/nike_image.png" alt="">
                    <p class="Name">Nike</p>
                    <p class="Price">RM 100</p>
                    <input type="button" class="cartButton" value="Quick Add">
                </div>
                
                <div class="recomend-img">
                    <img src="images/nike_image.png" alt="">
                    <p class="Name">Nike</p>
                    <p class="Price">RM 100</p>
                    <input type="button" class="cartButton" value="Quick Add">
                </div>
                
            </div>
        </div>
    
        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?> 
        <script src="product.js"></script>
</body>

</html>