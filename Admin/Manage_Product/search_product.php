<?php
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql = "SELECT* FROM product WHERE deleted=0 AND";
            
    $params = [];
    $types = ""; 

    foreach ($keywords as $keyword) {
        $sql .= "(product_id LIKE ? OR product_name LIKE ? OR product_type LIKE ? OR brand LIKE ? OR product_categories LIKE ? OR gender LIKE ?) AND ";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= "ssssss"; 
    }
 
    $sql = rtrim($sql, ' AND ');
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            $finalPrice = $row["discount_price"] > 0 ? $row["discount_price"] : $row["price"];
            echo '<tr>
                    <td>'. $row["product_id"].'</td>
                    <td><img src="../../upload/'. $row["product_img1"].'"</td>
                    <td>'. $row["product_name"].'</td>
                    <td style="color:orangered; font-weight:bold;">'. $row["brand"].'</td>
                    <td>'. $row["product_categories"].'</td>
                    <td>'. $row["gender"].'</td>
                    <td>'. $row["status"].'</td>
                    <td style="color:red; font-weight:bold;">'. number_format($finalPrice,2).'</td>
                    <td><div class="button"><a href="view_stock.php?id='.$row["product_id"].'" class="stock-button"><i class="fa-solid fa-boxes-packing"></i></a>
                    <a href="edit.php?id='.$row["product_id"].'" id="edit"><i class="fa-solid fa-pen"></i></a>
                    <a href="delete.php?id='.$row["product_id"].'" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a></div></td>
                </tr>';
        }
    } else {
        echo '<p>No product found.</p>';
    }
    echo '</div></div>';
}
?>
