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
    <?php
    // Include CSRF protection
    require_once __DIR__ . '/../app/csrf.php';
    // Generate CSRF token and add it to meta tag
    $csrf_token = generateCsrfToken();
    ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>

<?php
    include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $product_id=$_GET['id'];
        $current_product_id = $_GET['id'];

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
        
        // Check if user is logged in
        $is_authenticated = isset($_SESSION['user_id']);
        
        include __DIR__ . '/../Header_and_Footer/header.php'; 
        ?>

        <!-- Cart notification container -->
        <div class="message-container"></div>

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
                
                <!-- Add hidden CSRF token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button class="add-to-cart" <?php if (!$is_authenticated) echo 'data-requires-auth="true"'; ?>>Add to Cart</button>
                
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

                <?php
                    $product_id = $row['product_id'];

                    $sql1 = "SELECT rating, COUNT(*) as count FROM review WHERE product_id = $product_id GROUP BY rating";
                    $result1 = $conn->query($sql1);

                    $ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    $total = 0;
                    $sum = 0;

                    while ($row1 = $result1->fetch_assoc()) {
                        $ratings[$row1['rating']] = $row1['count'];
                        $sum += $row1['rating'] * $row1['count'];
                        $total += $row1['count'];
                    }

                    $average = $total ? round($sum / $total, 2) : 0;
                    $percent5 = $total ? round(($ratings[5] / $total) * 100) : 0;
                    $percent4 = $total ? round(($ratings[4] / $total) * 100) : 0;
                    $percent3 = $total ? round(($ratings[3] / $total) * 100) : 0;
                    $percent2 = $total ? round(($ratings[2] / $total) * 100) : 0;
                    $percent1 = $total ? round(($ratings[1] / $total) * 100) : 0;
                ?>

                <div class="tab-content" id="review">
                    <div class="ratingWrapper">
                    <h2>Review</h2>
                        <div class="rating-summary">
                            <div class="rating-averange"><?php echo number_format($average, 1);?></div>
                        </div>
                        <div class="ratingContainer">
                            <div class="ratingbar">
                                <span>★★★★★</span>
                                <div class="bar"><div class="fill" style="width: <?php echo $percent5; ?>%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★★★☆</span>
                                <div class="bar"><div class="fill" style="width: <?php echo $percent4; ?>%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★★☆☆</span>
                                <div class="bar"><div class="fill" style="width: <?php echo $percent3; ?>%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★★☆☆☆</span>
                                <div class="bar"><div class="fill" style="width: <?php echo $percent2; ?>%;"></div></div>
                            </div>
                            <div class="ratingbar">
                                <span>★☆☆☆☆</span>
                                <div class="bar"><div class="fill" style="width: <?php echo $percent1; ?>%;"></div></div>
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
                    $sql = "SELECT * FROM product p
                        WHERE p.product_id!='$current_product_id'
                        AND EXISTS (
                            SELECT 1 FROM stock s
                            WHERE s.product_id = p.product_id AND s.stock > 0
                            )
                            ORDER BY RAND()
                            LIMIT 4";

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
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?> 
        <script src="product.js"></script>
</body>

<?php
        }
    }
?>
<style>
    /* Add styles for the cart notification */
    .message-container {
        position: fixed;
        top: 70px; 
        right: 20px;
        left: auto;
        width: 300px;
        z-index: 999;
        text-align: center;
    }
    .message {
        padding: 12px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        animation: fadeIn 0.3s ease-out;
        display: block;
        width: 100%;
        box-sizing: border-box;
    }
    .message.success {
        background-color: #28a745;
        color: white;
    }
    .message.error {
        background-color: #dc3545;
        color: white;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sizeSelect = document.getElementById("size");
    const skuDisplay = document.getElementById("skuDisplay");
    const addToCartBtn = document.querySelector('.add-to-cart');

    function updateSKU() {
        const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
        const sku = selectedOption.getAttribute("data-sku") || "N/A";
        skuDisplay.textContent = sku;
    }

    updateSKU(); 
    sizeSelect.addEventListener("change", updateSKU);
    
    // Handle authentication for add to cart
    if (addToCartBtn && addToCartBtn.getAttribute('data-requires-auth')) {
        addToCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Redirect to login page with return URL
            window.location.href = '/FYP/FYP/User/login/login.php?redirect=' + encodeURIComponent(window.location.href);
        });
    }
});
</script>



</html>