<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql= "SELECT o.*,u.user_id, u.first_name,u.last_name,u.mobile_number FROM orders o
            JOIN users u ON o.user_id= u.user_id WHERE";
            
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
            $orderId = $row["order_id"];
            $statusColor = "";
            $status= $row['delivery_status'];

            switch ($status) {
                case "prepare":
                    $statusColor = "background-color: #DC3545; font-weight: bold; padding: 8px 16px; border-radius: 4px; color:white;";
                    break;
                case "packing":
                    $statusColor = "background-color:rgb(255, 128, 25); font-weight: bold; padding: 8px 15px; border-radius: 4px; color:white;";
                    break;
                case "assign":
                    $statusColor = "background-color: #FFC107; font-weight: bold; padding: 8px 20px; border-radius: 4px; color:white;";
                    break;
                case "shipping":
                    $statusColor = "background-color:rgb(0, 199, 50); font-weight: bold; padding: 8px 12px; border-radius: 4px; color:white;";
                    break;
                case "delivered":
                    $statusColor = "color:rgb(7, 196, 0); font-weight: bold; padding: 8px 10px; border-radius: 4px;" ;
                    break;
                default:
                    $statusColor = "background-color: red; font-weight: bold; padding: 8px 10px; border-radius: 4px;";
                    break;
            }

            echo '<tr>
                    <td>'. $row["order_id"].'</td>
                    <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="items">View</a></td>
                    <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                    <td>'.$row['mobile_number'].'</td>
                    <td>'. $row["shipping_address"].'</td>
                    <td><span style="'.$statusColor.'";>'. $row["delivery_status"].'</td>
                    <td>';
                    if (strtolower($row["delivery_status"]) === "delivered") {
                        echo '<span style="color: green; font-weight: bold;">Complete</span>';
                    }else {
                        echo '<a href="#" class="update" order_id="'.$row['order_id'].'">Update</a>';
                    }
                
                    echo    '</td>
                        </tr>';
        }
    } else {
        echo '<p>No order found.</p>';
    }
    echo '</div></div>';
}
?>
