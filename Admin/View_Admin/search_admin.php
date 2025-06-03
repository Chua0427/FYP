<?php

require_once __DIR__ . '/../auth_check.php';
// Start session if not already started

require_once __DIR__ . '/../protect.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_user_type= $_SESSION['user_type'];

include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql = "SELECT* FROM users WHERE user_type=2 AND";
            
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
        while($row= $result->fetch_assoc()){
            echo '<tr>
                    <td><img src="../../upload/'.$row['profile_image'].'"</td>
                    <td>'.$row['first_name']." ".$row['last_name'].'</td>
                    <td>'.$row['email'].'</td>
                    <td>'.$row['mobile_number'].'</td>
                    <td>'.$row['address'].'</td>
                    <td>'.$row['postcode'].'</td>
                    <td>'.$row['state'].'</td>
                    <td>'.$row['city'].'</td>
                    <td>'.$row['birthday_date'].'</td>
                    <td>'.$row['gender'].'</td>';


                    if ($current_user_type == 3) {
                        echo '<td>
                                <div class="button">
                                    <a href="edit_admin.php?id= '.$row['user_id'].' "class="btn btn-edit" id="edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="delete.php?id='. $row['user_id'].' "class="btn btn-delete" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a>
                                </div>
                                
                              </td>';
                    }
            
                    echo '</tr>';
        }
    } else {
        echo '<p>No users found.</p>';
    }
    echo '</div></div>';
}
?>
