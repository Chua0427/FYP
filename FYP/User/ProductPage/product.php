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

<?php
    include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $product_id=$_GET['id'];

        $sql="SELECT* FROM product WHERE product_id=$product_id";
        $result= $conn->query($sql);
        $row= $result->fetch_assoc();

        if($row){
?>

<body>
        <?php
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        include __DIR__ . '/../Header_and_Footer/header.php'; 
        ?>

        <div class="productContainer">
        
            <div class="imgContainer">
                <i class="fa fa-arrow-left" id="productButton"></i>
                <img id="main_image" src="../../upload/<?php echo $row['product_img1']?>">
                <i class="fa fa-arrow-right" id="productnextButton"></i>

                <div class="small-img-group">
                    <div class="small-column">
                        <img class="small-img" src="../../upload/<?php echo $row['product_img1']?>">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="../../upload/<?php echo $row['product_img2']?>">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="../../upload/<?php echo $row['product_img3']?>">
                    </div>
                    <div class="small-column">
                        <img class="small-img" src="../../upload/<?php echo $row['product_img4']?>">
                    </div>
                </div>
            </div>
            
            <div class="productDetails">
                
                <h1 id="brand"><?php echo $row['brand']?>
                    <?php if ($row['status'] === 'Promotion') {
                         $price = $row['price'];
                         $discount_price = $row['discount_price'];
     
                         $discountPercent = round((($price - $discount_price) / $price) * 100);
                        echo '<span style="color: white; background-color:red; padding:10px; border-radius:10px; font-size: 18px; margin-left: 10px;">'.$discountPercent.'% OFF</span>';
                    } ?></h1>
                    
                <h3 id="name"><?php echo $row['product_name']?></h3>
                <span>SKU :</span><span id="skuDisplay"></span><p style="padding-top: 20px;"></p>

                <div class="column">
                <?php if ($row['status'] === 'Promotion') { ?>
                    <p style="margin: 5px 0 10px 0;">
                        <span style="color: #e60000; font-size: 20px; font-weight: bold;">RM </span>
                        <span id="price" style="margin-right: -200px;"><?php echo $row['discount_price']; ?>
                        </span><span style="text-decoration: line-through; color: gray; font-size: 18px;">RM <?php echo $row['price']; ?></span>
                    </p>
                <?php } 
                    else { ?>
                        <span style="color: #e60000; font-size: 20px; font-weight: bold;">RM </span>
                        <span id="price"><?php echo $row['price']; ?></span>
                    <?php } ?>
                
                <span class="size-chart-icon" onclick="openModal()"><i class="fa-solid fa-ruler-combined"></i></span>
                </div>
                
                <div class="sizeContainer">
                    <label style="margin-top: 8px;">Size:</label>
                    <select id="size">
                    <?php
                        $stock_sql = "SELECT product_size, product_sku, stock FROM stock WHERE product_id = $product_id AND stock>0";
                        $stock_result = $conn->query($stock_sql);

                        while($stock_row = $stock_result->fetch_assoc()) {
                            $size = $stock_row['product_size'];
                            $sku = $stock_row['product_sku'];
                            $stock = $stock_row['stock'];
                            $display_text = str_pad("$size", 10) . " ($stock)";
                            echo "<option value='$size' data-sku='$sku'>$display_text</option>";
                        }
                    ?>
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
                    <p><?php echo $row['description']?></p>
                </div>

                <div class="tab-content" id="review">
                    <div class="ratingWrapper">
                    <h2>Review</h2>
                        <div class="rating-summary">
                            <div class="rating-averange">5.0</div>
                        </div>
                        <div class="ratingContainer">
                            <div class="ratingbar">
                                <span>★★★★★</span>
                                <div class="bar"><div class="fill" style="width: 100%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★★★☆</span>
                                <div class="bar"><div class="fill" style="width: 50%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★★☆☆</span>
                                <div class="bar"><div class="fill" style="width: 30%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★☆☆☆</span>
                                <div class="bar"><div class="fill" style="width: 10%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★☆☆☆☆</span>
                                <div class="bar"><div class="fill" style="width: 5%;"></div></div>
                            </div>
                        </div> 
                    </div>
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
                <img id="size-chart-img" src="../../upload/<?php echo $row['size_chart']?>" alt="Size Chart" class="size-chart-img">
            </div>
        </div>

        <div class="recomend-container">
            <h1>Recomended</h1>
            <div class="recomend-column">
            <?php
                $sql="SELECT* FROM product ORDER BY RAND() LIMIT 4";
                $result= $conn->query($sql);
                
                while($row= $result->fetch_assoc()){
            ?>

                <div class="recomend-img">
                <?php if ($row['status'] === 'Promotion'){
                    $price = $row['price'];
                    $discount_price = $row['discount_price'];

                    $discountPercent = round((($price - $discount_price) / $price) * 100);
                    echo '<div style="position: absolute; left:1px; background-color: red; color: white; font-size: 14px; font-weight: bold; padding: 5px 10px; border-radius: 5px; z-index: 1;">'.$discountPercent.' % OFF</div>';
                    }
                 ?>
                 
                    <a href="../ProductPage/product.php?id=<?php echo $row['product_id']?>">
                    <img src="../../upload/<?php echo $row['product_img1'] ?>" alt="">
                    <p class="Name"><?php echo $row['product_name'] ?></p>
                    
                        <?php
                            if($row['status'] === 'Promotion'){
                                echo '<p class="Price">RM '.$row['discount_price'].'</span>
                                <span style="color: gray; text-decoration: line-through; margin-left:20px">RM '.$row['price'].'</span></p>';
                            }
                            else{
                                echo '<p class="Price">RM '.$row['price'].'</p>';
                            }
                        ?>
                    
                    
                    </a>
                </div>
        <?php
                }
        ?>
            </div>
        </div>
        <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?> 
        <script src="product.js"></script>
</body>

<?php
    }
}
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const sizeSelect = document.getElementById("size");
    const skuDisplay = document.getElementById("skuDisplay");

    function updateSKU() {
        const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
        const sku = selectedOption.getAttribute("data-sku") || "N/A";
        skuDisplay.textContent = sku;
    }

    updateSKU(); 
    sizeSelect.addEventListener("change", updateSKU); 
});
</script>



</html>