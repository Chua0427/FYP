<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="review.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>
    <div class="formWrapper">
        <div class="formContainer">
            <h2>Review</h2>
            <h3>Click star to rate the product !</h3>

            <input type="hidden" id="product_id" name="product_id" value="<?= $product_id ?>">
            
            <form action="submit_review.php" method="post">
                <div class="star-rating">
                    <i class="fas fa-star star" data-value="5"></i>
                    <i class="fas fa-star star" data-value="4"></i>
                    <i class="fas fa-star star" data-value="3"></i>
                    <i class="fas fa-star star" data-value="2"></i>
                    <i class="fas fa-star star" data-value="1"></i>
                </div>
            </form>
            
            <input type="hidden" id="ratingValue" name="ratingValue" value="0"> 
            
            <h3>Share your review about recently purchased product !</h3>
            <input type="text" id="review" placeholder="Please write your comment..."></input><p></p>
            <button type="submit" class="submit-btn">Submit</button> 
        </div>
    </div>
    
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?>
    <script src="review.js"></script>
</body>

</html>