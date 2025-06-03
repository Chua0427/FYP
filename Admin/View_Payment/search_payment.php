<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql= "SELECT* FROM payment WHERE";
            
    $params = [];
    $types = ""; 

    foreach ($keywords as $keyword) {
        $sql .= "(order_id LIKE ? ) AND ";
        $params[] = "%$keyword%";
        $types .= "s"; 
    }
 
    $sql = rtrim($sql, ' AND ');
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            echo '
                <tr>
                    <td>'.$row['payment_id'].'</td>
                    <td>'.$row['order_id'].'</td>
                    <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="view_item">View</a></td>
                    <td style="font-weight: bold;"> RM '.$row['total_amount'].'</td>
                    <td>'.$row['payment_at'].'</td>
                    <td><span style="background-color:rgb(35, 161, 31); color: white; padding:10px; border-radius:10px; font-weight: bold;">'.$row['payment_status'].'</span></td>
                </tr>';
        }
    } else {
        echo '<p>No order found.</p>';
    }
    echo '</div></div>';
}
?>
