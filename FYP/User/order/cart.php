<?php
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require __DIR__ . '/../app/init.php';

session_start();



try {
    // Initialize Database
    $db = new Database();
    
    // Fetch cart items for the logged-in user with product details
    $cartItems = $db->fetchAll(
        "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
         CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
         FROM cart c 
         JOIN product p ON c.product_id = p.product_id 
         WHERE c.user_id = ? 
         ORDER BY p.brand, c.added_at DESC", 

    );
    
    // Group items by brand/store
    $cartByStore = [];
    $totalItems = 0;
    $totalPrice = 0;
    $totalOriginalPrice = 0;
    
    foreach ($cartItems as $item) {
        $storeName = $item['brand'] ?? 'Other';
        
        if (!isset($cartByStore[$storeName])) {
            $cartByStore[$storeName] = [];
        }
        
        $cartByStore[$storeName][] = $item;
        $totalItems += $item['quantity'];
        $totalPrice += $item['final_price'] * $item['quantity'];
        $totalOriginalPrice += $item['price'] * $item['quantity'];
    }
    
    $totalDiscount = $totalOriginalPrice - $totalPrice;

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Process actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            $db = new Database();
            
            switch ($_POST['action']) {
                case 'remove':
                    if (isset($_POST['cart_id'])) {
                        $db->execute(
                            "DELETE FROM cart WHERE cart_id = ? AND user_id = ?", 
                            [$_POST['cart_id'], $user_id]
                        );
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    break;
                    
                case 'update_quantity':
                    if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
                        $quantity = max(1, (int)$_POST['quantity']);
                        $db->execute(
                            "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?", 
                            [$quantity, $_POST['cart_id'], $user_id]
                        );
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    break;
                    
                case 'checkout':
                    // Redirect to checkout page
                    header('Location: /FYP/User/payment/checkout.php');
                    exit;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Format price in MYR
function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
}


$pageTitle = "Shopping Cart - VeroSports";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <?php include_once '../Header_and_Footer/header.html'; ?>
    
    <main>
        <div class="container">
            <h1>Shopping Cart</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="/FYP/User/product.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <?php foreach ($cartByStore as $storeName => $items): ?>
                        <div class="store">
                            <h3>
                                <input type="checkbox" checked> <?php echo htmlspecialchars($storeName); ?> Store
                            </h3>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="product">
                                    <img src="<?php echo !empty($item['product_img1']) ? htmlspecialchars($item['product_img1']) : 'https://via.placeholder.com/100'; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <div class="product-details">
                                        <p class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="product-variant">Size: <?php echo htmlspecialchars($item['product_size']); ?></p>
                                        <div class="price">
                                            <?php echo formatPrice($item['final_price']); ?>
                                            <?php if ($item['discount_price'] && $item['discount_price'] < $item['price']): ?>
                                                <del><?php echo formatPrice($item['price']); ?></del>
                                            <?php endif; ?>
                                        </div>
                                        <div class="actions">
                                            <div class="quantity-control">
                                                <button type="button" class="quantity-btn minus" data-cart-id="<?php echo $item['cart_id']; ?>">-</button>
                                                <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                                <button type="button" class="quantity-btn plus" data-cart-id="<?php echo $item['cart_id']; ?>">+</button>
                                            </div>
                                            <button type="submit" name="remove_item" class="remove-btn" data-cart-id="<?php echo $item['cart_id']; ?>">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="sidebar">
                        <h4>Order Summary</h4>
                        <div class="row">
                            <span>Total Items (<?php echo $totalItems; ?>)</span>
                            <span><?php echo formatPrice($totalOriginalPrice); ?></span>
                        </div>
                        <?php if ($totalDiscount > 0): ?>
                        <div class="row">
                            <span>Total Discount</span>
                            <span>-<?php echo formatPrice($totalDiscount); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="row" style="font-weight: bold; font-size: 1.1rem;">
                            <span>Total</span>
                            <span><?php echo formatPrice($totalPrice); ?></span>
                        </div>
                        <button type="submit" name="action" value="checkout" class="checkout-btn">Checkout</button>
                    </div>
                    
                    <!-- Hidden forms for item actions -->
                    <div id="action-forms" style="display: none;">
                        <form id="remove-form" method="post">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" id="remove-cart-id" name="cart_id" value="">
                        </form>
                        <form id="update-form" method="post">
                            <input type="hidden" name="action" value="update_quantity">
                            <input type="hidden" id="update-cart-id" name="cart_id" value="">
                            <input type="hidden" id="update-quantity" name="quantity" value="">
                        </form>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once '../Header_and_Footer/footer.html'; ?>
    
    <script src="cart.js"></script>
</body>
</html>