<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../payment/db.php';

// Check if user is authenticated
Auth::requireAuth();

// Set up logger for this page
$logger = $GLOBALS['logger'];

// Get the product ID from URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$user_id = (int)$_SESSION['user_id'];

// Initialize error and success messages
$error = '';
$success = '';

// Validate the product exists and user purchased it
try {
    // Initialize database connection
    $db = new Database();
    
    // Check if product exists
    $product = $db->fetchOne(
        "SELECT * FROM product WHERE product_id = ?",
        [$product_id]
    );
    
    if (!$product) {
        throw new Exception("Product not found");
    }
    
    // Check if the user has purchased this product
    $hasPurchased = $db->fetchOne(
        "SELECT oi.order_id FROM order_items oi 
         JOIN orders o ON oi.order_id = o.order_id 
         WHERE o.user_id = ? AND oi.product_id = ? AND o.delivery_status = 'delivered'
         LIMIT 1",
        [$user_id, $product_id]
    );
    
    if (!$hasPurchased) {
        throw new Exception("You can only review products you have purchased and received");
    }
    
    // Check if user has already reviewed this product
    $existingReview = $db->fetchOne(
        "SELECT * FROM review WHERE user_id = ? AND product_id = ?",
        [$user_id, $product_id]
    );
    
    // If this is a form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }
        
        // Get and validate form data
        $rating = isset($_POST['ratingValue']) ? (int)$_POST['ratingValue'] : 0;
        $reviewText = isset($_POST['review']) ? trim($_POST['review']) : '';
        
        if ($rating < 1 || $rating > 5) {
            $error = "Please select a valid rating from 1 to 5 stars";
        } elseif (empty($reviewText)) {
            $error = "Please enter your review text";
        } else {
            // If user already reviewed, update the review
            if ($existingReview) {
                $result = $db->execute(
                    "UPDATE review SET rating = ?, review_text = ?, review_at = CURRENT_TIMESTAMP 
                     WHERE user_id = ? AND product_id = ?",
                    [$rating, $reviewText, $user_id, $product_id]
                );
                $success = "Your review has been updated";
            } else {
                // Insert new review
                $result = $db->execute(
                    "INSERT INTO review (user_id, product_id, rating, review_text) 
                     VALUES (?, ?, ?, ?)",
                    [$user_id, $product_id, $rating, $reviewText]
                );
                $success = "Thank you for your review!";
            }
            
            // Log the review action
            $logger->info('User submitted product review', [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'rating' => $rating,
                'action' => $existingReview ? 'update' : 'new'
            ]);
            
            // Redirect back to order history after a small delay (for the success message)
            echo "<script>alert('" . htmlspecialchars($success) . "'); window.location.href='../order/orderhistory.php';</script>";
            exit;
        }
    }
    
} catch (Exception $e) {
    $logger->error('Error in product review page', [
        'user_id' => $user_id,
        'product_id' => $product_id,
        'error' => $e->getMessage()
    ]);
    
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="write_review.css">
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="formWrapper">
        <div class="formContainer">
            <h2>Review</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($product)): ?>
                <div class="product-details">
                    <img src="../../upload/<?php echo htmlspecialchars($product['product_img1']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <div>
                        <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                        <p><?php echo htmlspecialchars($product['brand']); ?></p>
                    </div>
                </div>
                
                <h3>Click star to rate the product!</h3>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="star-rating">
                        <i class="fas fa-star star" data-value="5"></i>
                        <i class="fas fa-star star" data-value="4"></i>
                        <i class="fas fa-star star" data-value="3"></i>
                        <i class="fas fa-star star" data-value="2"></i>
                        <i class="fas fa-star star" data-value="1"></i>
                    </div>
                    
                    <input type="hidden" id="ratingValue" name="ratingValue" value="<?php echo isset($existingReview) ? htmlspecialchars((string)$existingReview['rating']) : '0'; ?>">
                    
                    <h3>Share your review about recently purchased product!</h3>
                    <textarea id="review" name="review" placeholder="Please write your comment..."><?php echo isset($existingReview) ? htmlspecialchars($existingReview['review_text']) : ''; ?></textarea>
                    
                    <button type="submit" class="submit-btn">
                        <?php echo isset($existingReview) ? 'Update Review' : 'Submit'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    
    <script src="write_review.js"></script>
</body>
</html> 