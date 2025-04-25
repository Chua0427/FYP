<?php
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);

    if ($search === '') {
        echo '<p>Please enter a keyword to search.</p>';
        exit;
    }
    
    $keywords = explode(' ', $search); // 将用户输入的多个词分割成数组
    
    // 创建一个查询条件，适用于多个关键词
    $sql = "SELECT p.* FROM product p WHERE EXISTS (
            SELECT 1 FROM stock s
            WHERE s.product_id = p.product_id AND s.stock > 0) AND";
            
    $params = [];
    $types = ""; // 用来存储参数的类型（s表示string）

    // 为每个关键词添加一个 LIKE 查询条件
    foreach ($keywords as $keyword) {
        $sql .= "(product_name LIKE ? OR product_type LIKE ? OR brand LIKE ? OR product_categories LIKE ?) AND ";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= "ssss"; // 4个 's' 表示四个字符串参数
    }

    // 去掉最后的 "AND " 
    $sql = rtrim($sql, ' AND ');
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="product-wrapper"><div class="product-container">';
    if ($result->num_rows > 0) {
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
                        <div class="price">RM'.$row['price'].'</div>
                    </a>
                    </div>';
            }
        }
    } else {
        echo '<p>No products found.</p>';
    }
    echo '</div></div>';
}
?>
