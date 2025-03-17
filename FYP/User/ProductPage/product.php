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
            <i class="fa fa-arrow-left" id="imgButton"></i>
            <img id="main_image" src="images/nike_image.png">
            <i class="fa fa-arrow-right" id="imgnextButton"></i>

            <div class="thumbnail-container">
                <img class="thumbnail" src="images/nike_image.png">
                <img class="thumbnail" src="images/nike_image.png">
                <img class="thumbnail" src="images/nike_image.png">
                <img class="thumbnail" src="images/nike_image.png">
            </div>
        </div>
        
        <div class="productDetails">
            <h1 id="brand">Nike</h1>
            <h3 id="name">Nike Phantom GX 2</h3>
            <span>SKU :</span><span id="sku">12345678</span>
            <p id="price"><span>RM </span>99.99</p>
            <p id="description">The product is the best.</p>

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
        </div>
        </div>
    
        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?> 
</body>

</html>