<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);
    
    $keywords = explode(' ', $search); 
    
    $sql= "SELECT p.*, AVG(r.rating) AS avr_rating
        FROM product p
        LEFT JOIN review r ON p.product_id = r.product_id
        WHERE p.deleted=0 AND";
            
    $params = [];
    $types = ""; 

    foreach ($keywords as $keyword) {
        $sql .= "(p.product_id LIKE ? OR product_name LIKE ?) AND ";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= "ss"; 
    }
 
    $sql = rtrim($sql, ' AND ');

    $sql .= " GROUP BY p.product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            $user_rating = round($row['avr_rating']);

                echo '<tr>
                    <td><img src="../../upload/'.$row['product_img1'].'">
                    </td>
                    <td><span class="name">'.$row['product_name'].'<span></td>
                    <td><div class="rating">';
                        for ($i = 1; $i <= 5; $i++) {
                            $filled = $i <= $user_rating ? 'filled' : '';
                            echo "<span class='star $filled' data-value='$i'>&#9733;</span>";
                        }
                echo '  </div>
                            </td>
                            <td>
                                <a class="review-button" href="view_review.php?id='.$row['product_id'].'"><i class="fa-solid fa-eye"></i></a>
                            </td>
                    </tr>';
                        }
    } else {
        echo '<p>No order found.</p>';
    }
    echo '</div></div>';
}
?>
