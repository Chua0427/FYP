<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_review.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
<?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

<div class="contain">
    <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

    <?php
        $product_id= $_GET['id'];

        $sql="SELECT p.*, AVG(r.rating) AS avr_rating
              FROM product p
              LEFT JOIN review r ON p.product_id= r.product_id
              WHERE p.product_id= $product_id
              GROUP BY p.product_id";
        $result= $conn->query($sql);
        $row= $result->fetch_assoc();


        $sql1="SELECT r.*,u.profile_image,u.first_name,u.last_name,u.email
               FROM review r
               LEFT JOIN users u ON r.user_id= u.user_id
               WHERE r.product_id= $product_id
               ORDER BY r.review_id DESC";
        $result1= $conn->query($sql1);

    ?>

    <div class="review-container">
        <div class="container">
            <div class="product">
                <img src="../../upload/<?php echo $row['product_img1'] ?>">
                <p style="color: orangered; font-weight: bold; font-size: 18px;"><?php echo $row['brand'] ?></p>
                <p><?php echo $row['product_name'] ?></p>
            </div>
            <div class="avr-review">
                <h2><?php echo round($row['avr_rating'],1) ?></h2>
                    <div class="rating">
                        <?php
                        $user_rating= $row['avr_rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            $filled = $i <= $user_rating ? 'filled' : '';
                            echo "<span class='star $filled' data-value='$i'>&#9733;</span>";
                        }
                        ?>
                    </div>
            </div>
        </div> 
        <h3>Review</h3>
        <div class="review">
            <table>
                <?php while ($row1 = $result1->fetch_assoc()){ 
                    $image = !empty($row1['profile_image']) ? $row1['profile_image'] : 'default.jpg';?>
                <tr>
                    <td><img src="../../upload/<?php echo $image ?>" alt=""><p></p>
                        <p><?php echo $row1['first_name'] . " " . $row1['last_name'] ?></p>
                            <?php
                                $user_rating= $row1['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        $filled = $i <= $user_rating ? 'filled' : '';
                                        echo "<span class='user-star $filled' data-value='$i'>&#9733;</span>";
                                     }
                        ?>
                    </td>
                    <td>
                        <span class="comment"><?php echo $row1['review_text'] ?></span>
                    </td>
                    
                </tr>
                <?php }?>
            </table>
        </div>
    </div>
</div>

</body>
</html>