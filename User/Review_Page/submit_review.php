<?php
    include __DIR__ . '/../../connect_db/config.php';

    session_start();
    $user = $_SESSION['user_id'];

    if(isset($_GET['product_id']))
    {
        $user_id= $user;
        $product_id= $_GET['product_id'];

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $rating= $_POST['ratingValue'];
            $review= $_POST['review'];

            $sql= "INSERT INTO review (user_id,product_id,rating,review_text)
            VALUE ('$user_id','$product_id','$rating','$review')";

            $result= $conn->query($sql);

            if($result){
                echo "<script>alert('Review Submit Successfullt! Thank You For Your Feedback.'); window.history.go(-2);</script>";
            }else{
                echo "Error :" . $conn->error;
            }

        }
    }
?>