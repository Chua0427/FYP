<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql = "SELECT* FROM users WHERE user_type=1 AND";
            
    $params = [];
    $types = ""; 

    foreach ($keywords as $keyword) {
        $sql .= "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? ) AND ";
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
        while($row = $result->fetch_assoc()) {
            $image = !empty($row['profile_image']) ? $row['profile_image'] : 'default.jpg';
            echo '<tr>
                    <td><img src="../../upload/'.$image.'"></td>
                    <td>'.$row['first_name']." ".$row['last_name'].'</td>
                    <td>'.$row['email'].'</td>
                    <td>'.$row['mobile_number'].'</td>
                    <td>'.$row['address'].'</td>
                    <td>'.$row['postcode'].'</td>
                    <td>'.$row['state'].'</td>
                    <td>'.$row['city'].'</td>
                    <td>'.$row['birthday_date'].'</td>
                    <td>'.$row['gender'].'</td>
                </tr>';
        }
    } else {
        echo '<p>No users found.</p>';
    }
    echo '</div></div>';
}
?>
