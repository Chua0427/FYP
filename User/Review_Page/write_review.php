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
            header("Refresh: 2; URL=../order/orderhistory.php");
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .review-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-right: 20px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        
        .product-details h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .product-brand {
            color: #777;
            margin: 0 0 5px 0;
        }
        
        .star-rating {
            margin: 25px 0;
            text-align: center;
        }
        
        .star {
            font-size: 35px;
            color: #ddd;
            cursor: pointer;
            margin: 0 5px;
            transition: color 0.2s;
        }
        
        .star.selected {
            color: #ffc107;
        }
        
        .rating-text {
            text-align: center;
            margin: 10px 0 25px;
            font-size: 16px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: #0077cc;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: block;
            margin: 0 auto;
        }
        
        .submit-btn:hover {
            background-color: #005fa3;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0077cc;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="review-container">
        <h1 class="page-title">Write a Review</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php else: ?>
            
            <?php if (isset($product)): ?>
                <div class="product-info">
                    <img src="../../upload/<?php echo htmlspecialchars($product['product_img1']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                    </div>
                </div>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="star-rating">
                        <i class="fas fa-star star" data-value="1"></i>
                        <i class="fas fa-star star" data-value="2"></i>
                        <i class="fas fa-star star" data-value="3"></i>
                        <i class="fas fa-star star" data-value="4"></i>
                        <i class="fas fa-star star" data-value="5"></i>
                    </div>
                    
                    <p class="rating-text">Select your rating</p>
                    
                    <input type="hidden" id="ratingValue" name="ratingValue" value="0">
                    
                    <div class="form-group">
                        <label for="review">Your Review</label>
                        <textarea id="review" name="review" placeholder="Share your experience with this product..."><?php echo isset($existingReview) ? htmlspecialchars($existingReview['review_text']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <?php echo isset($existingReview) ? 'Update Review' : 'Submit Review'; ?>
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        
        <a href="../order/orderhistory.php" class="back-link">Back to Order History</a>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingValue = document.getElementById('ratingValue');
            const ratingText = document.querySelector('.rating-text');
            
            // Set initial rating if editing
            <?php if (isset($existingReview)): ?>
            const initialRating = <?php echo (int)$existingReview['rating']; ?>;
            setRating(initialRating);
            <?php endif; ?>
            
            // Handle star clicks
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    setRating(value);
                });
                
                star.addEventListener('mouseover', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    highlightStars(value);
                });
                
                star.addEventListener('mouseout', function() {
                    const currentRating = parseInt(ratingValue.value) || 0;
                    highlightStars(currentRating);
                });
            });
            
            function setRating(value) {
                ratingValue.value = value;
                highlightStars(value);
                
                // Update text based on rating
                const ratingTexts = [
                    'Select your rating',
                    'Poor - 1 star',
                    'Fair - 2 stars',
                    'Average - 3 stars',
                    'Good - 4 stars',
                    'Excellent - 5 stars'
                ];
                
                ratingText.textContent = ratingTexts[value] || ratingTexts[0];
            }
            
            function highlightStars(count) {
                stars.forEach(star => {
                    const starValue = parseInt(star.getAttribute('data-value'));
                    if (starValue <= count) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }
        });
    </script>
</body>
</html> 