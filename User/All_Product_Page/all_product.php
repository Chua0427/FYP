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
    <?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    include __DIR__ . '/../Header_and_Footer/header.php'; 
    ?>

    <?php
        $category = isset($_GET['product_categories']) ? $_GET['product_categories'] : '';
        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
        $brand = isset($_GET['brand']) ? $_GET['brand']:'';
        
        $title = '';

        if (!empty($gender)) {
            $title .= $gender . ' ';
        }
        if (!empty($category)) {
            $title .= $category . ' ';
        }
        if (!empty($brand)) {
            $title .= $brand;
        }

        $title = trim($title);

        if (empty($title)) {
            $title = "All Product";
        }


        include __DIR__ . '/../../connect_db/config.php';

        $sql = "SELECT * FROM product p
                WHERE EXISTS (
                    SELECT 1 FROM stock s
                    WHERE s.product_id = p.product_id AND s.stock > 0 AND deleted=0
                )";


        if (!empty($gender)) {
            $sql .= " AND gender = '$gender'";
        }
        if (!empty($category)) {
            $sql .= " AND product_categories = '$category'";
        }
        if (!empty($brand)) {
            $sql .= " AND brand = '$brand'";
        }

        $result= $conn->query($sql);

        //check available categries
        $availableCategories = [];

        $sqlCategories = "SELECT DISTINCT p.product_categories FROM product p JOIN stock s ON p.product_id=s.product_id WHERE 1=1 AND s.stock>0 AND deleted=0";
        
        if (!empty($gender)) {
            $sqlCategories .= " AND gender = '$gender'";
        }
        if (!empty($brand)) {
            $sqlCategories .= " AND brand = '$brand'";
        }

        $categoryResult = $conn->query($sqlCategories);
        while ($row = $categoryResult->fetch_assoc()) {
            $availableCategories[] = $row['product_categories'];
        }

        //check available brand
        $availableBrands=[];

        $sqlBrands = "SELECT DISTINCT p.brand FROM product p JOIN stock s ON p.product_id=s.product_id WHERE 1=1 AND s.stock>0 AND deleted=0";
        
        if (!empty($gender)) {
            $sqlBrands .= " AND gender = '$gender'";
        }
        if (!empty($category)) {
            $sqlBrands .= " AND product_categories = '$category'";
        }
        

        $brandResult= $conn->query($sqlBrands);
        while($rowbrand = $brandResult->fetch_assoc()){
            $availableBrands[]= $rowbrand['brand'];
        }

        //check gender
        $availableGender=[];

        $sqlGender = "SELECT DISTINCT p.gender FROM product p JOIN stock s ON p.product_id=s.product_id WHERE 1=1 AND s.stock>0 AND deleted=0";
        
        if (!empty($brand)) {
            $sqlGender .= " AND brand = '$brand'";
        }
        if (!empty($category)) {
            $sqlGender .= " AND product_categories = '$category'";
        }
        

        $genderResult= $conn->query($sqlGender);
        while($rowgender = $genderResult->fetch_assoc()){
            $availableGender[]= $rowgender['gender'];
        }


    ?>


    <h2><?php echo $title; ?></h2>

    <button id="filterbtn" onclick="openfilter()"><i class="fa-solid fa-filter"></i><span>Filter</span></button>

    <div class="container">
        <div class="sidebar">
            <div class="sidebar-container">
                <div class="filter1">
                    <h3>Price</h3>
                    <P>RM <input type="number"  min="0" id="minprice"><span> - RM <input id="maxprice"type="number"  min="0"></span></P>
                </div>
                <div class="filter" id="category-filter">
                    <h3>Product Category</h3>
                    <?php
                        foreach ($availableCategories as $catOption) {
                            echo '<label><input type="checkbox" class="filter-checkbox" name="category" value="' . $catOption . '" > ' . $catOption . '</label><br>';
                        }
                    ?>
                </div>


                <div class="filter" id="gender-filter">
                    <h3>Gender</h3>
                    <?php
                        foreach ($availableGender as $genderOption) {
                            echo '<label><input type="checkbox" class="filter-checkbox" name="gender" value="' . $genderOption . '" > ' . $genderOption . '</label><br>';
                        }
                    ?>
                </div>
                <div class="filter" id="brand-filter">
                    <h3>Shop By Brand</h3>
                    <?php
                        foreach ($availableBrands as $brandOption) {
                            echo '<label><input type="checkbox" class="filter-checkbox" name="brand" value="' . $brandOption . '" > ' . $brandOption . '</label><br>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="product-wrapper">
            <div class="product-container">

            <?php
                while ($row = $result->fetch_assoc()) {
                        if($row['status']==='Promotion'){
                            $price = $row['price'];
                            $discount_price = $row['discount_price'];

                            $discountPercent = round((($price - $discount_price) / $price) * 100);

                            echo '<div class="product-column">
                                <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                                <div class="discount">'.$discountPercent.'% OFF</div>
                                    <img src="../../upload/'.$row['product_img1'].'" alt="">
                                    <p class="product-name">'.$row['product_name'].'</p>
                                    <div class="price">
                                        <span class="price">RM '.$row['discount_price'].'</span>
                                        <span class="discountPrice">RM '.$row['price'].'</span>
                                    </div>
                                    </a>
                                </div>';
                        }
                        else{
                            echo '<div class="product-column">
                                <a href="../ProductPage/product.php?id='.$row['product_id'].'">
                                    <img src="../../upload/'.$row['product_img1'].'" alt="">
                                    <p class="product-name">'.$row['product_name'].'</p>
                                    <div class="price">RM '.$row['price'].'</div>
                                </a>
                                </div>';
                        }
                    }
            ?>
                
            </div>
        </div>
    </div>
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?> 
        <script src="all_product.js"></script>
</body>

</html>