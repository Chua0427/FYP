<?php
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql= "SELECT o.*,u.first_name,u.last_name,u.mobile_number, u.profile_image FROM orders o
                               JOIN users u ON o.user_id= u.user_id WHERE";
            
    $params = [];
    $types = ""; 

    foreach ($keywords as $keyword) {
        $sql .= "(order_id LIKE ? OR first_name LIKE ? OR last_name LIKE ?) AND ";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= "sss"; 
    }
 
    $sql = rtrim($sql, ' AND ');
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            $orderId = $row["order_id"];
            $image = !empty($row['profile_image']) ? $row['profile_image'] : 'default.jpg';
            echo '<tr>
                    <td>'. $row["order_id"].'</td>
                    <td><img src="../../upload/'.$image.'"</td>
                    <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                    <td>'.$row['mobile_number'].'</td>
                    <td style="font-weight:bold;";>RM '. $row["total_price"].'</td>
                    <td>'. $row["order_at"].'</td>
                    <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="items">View</a></td>
                </tr>';
        }
    } else {
        echo '<p>No order found.</p>';
    }
    echo '</div></div>';
}
?>
