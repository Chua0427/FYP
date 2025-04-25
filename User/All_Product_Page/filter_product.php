<?php
include __DIR__ . '/../../connect_db/config.php';

$categories = $_POST['product_categories'] ?? [];
$genders = $_POST['gender'] ?? [];
$brands = $_POST['brand'] ?? [];
$minprice = isset($_POST['minprice']) && $_POST['minprice'] !== '' ? (int)$_POST['minprice'] : null;
$maxprice = isset($_POST['maxprice']) && $_POST['maxprice'] !== '' ? (int)$_POST['maxprice'] : null;

$sql = "SELECT p.*, MIN(s.stock) as stock 
        FROM product p
        LEFT JOIN stock s ON p.product_id = s.product_id
        WHERE s.stock > 0";

if (!empty($genders)) {
    $genderList = "'" . implode("','", $genders) . "'";
    $sql .= " AND p.gender IN ($genderList)";
}
if (!empty($categories)) {
    $catList = "'" . implode("','",$categories) . "'";
    $sql .= " AND p.product_categories IN ($catList)";
}
if (!empty($brands)) {
    $brandList = "'" . implode("','",  $brands) . "'";
    $sql .= " AND p.brand IN ($brandList)";
}
if ($minprice !== null) {
    $sql .= " AND IF(p.discount_price IS NOT NULL AND p.discount_price > 0, p.discount_price, p.price) >= $minprice";
}
if ($maxprice !== null) {
    $sql .= " AND IF(p.discount_price IS NOT NULL AND p.discount_price > 0, p.discount_price, p.price) <= $maxprice";
}


$sql .= " GROUP BY p.product_id";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];

    if ($row['status'] === 'Promotion') {
        $price = $row['price'];
        $discount_price = $row['discount_price'];
        $discountPercent = round((($price - $discount_price) / $price) * 100);
        echo '<div class="product-column">
                <a href="../ProductPage/product.php?id='.$product_id.'">
                    <div class="discount">'.$discountPercent.'% OFF</div>
                    <img src="../../upload/'.$row['product_img1'].'" alt=""/>
                    <p class="product-name">'.$row['product_name'].'</p>
                    <div class="price">
                        <span class="price">RM '.$discount_price.'</span>
                        <span class="discountPrice">RM '.$price.'</span>
                    </div>
                </a>
              </div>';
    } else {
        echo '<div class="product-column">
                <a href="../ProductPage/product.php?id='.$product_id.'">
                    <img src="../../upload/'.$row['product_img1'].'" alt=""/>
                    <p class="product-name">'.$row['product_name'].'</p>
                    <div class="price">RM '.$row['price'].'</div>
                </a>
              </div>';
    }
}
?>
