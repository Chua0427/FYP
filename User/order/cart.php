<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];
$csrf_token = generateCsrfToken();

try {
    // Initialize Database
    $db = new Database();
    
    // Get user information for shipping address
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE user_id = ?",
        [$user_id]
    );
    
    if (!$user) {
        throw new Exception("User information not found");
    }
    
    // Fetch cart items for the logged-in user with product details
    $cartItems = $db->fetchAll(
        "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
         CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price
         FROM cart c 
         JOIN product p ON c.product_id = p.product_id 
         WHERE c.user_id = ? 
         ORDER BY p.brand, c.added_at DESC", 
        [$user_id]
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
    // Verify CSRF token for all POST requests
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error = "Invalid security token. Please try again.";
    } else if (isset($_POST['action'])) {
        try {
            $db = new Database();
            $db->beginTransaction();
            
            switch ($_POST['action']) {
                case 'remove':
                    if (isset($_POST['cart_id'])) {
                        // Log attempt to remove item
                        $GLOBALS['logger']->info('Removing cart item', [
                            'user_id' => $user_id,
                            'cart_id' => $_POST['cart_id']
                        ]);
                        
                        $db->execute(
                            "DELETE FROM cart WHERE cart_id = ? AND user_id = ?", 
                            [$_POST['cart_id'], $user_id]
                        );
                        $db->commit();
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    break;
                    
                case 'update_quantity':
                    if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
                        $quantity = max(1, (int)$_POST['quantity']);
                        $cart_id = (int)$_POST['cart_id'];
                        
                        // Get the cart item first to check stock availability
                        $cartItem = $db->fetchOne(
                            "SELECT c.*, s.stock FROM cart c
                             JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
                             WHERE c.cart_id = ? AND c.user_id = ?", 
                            [$cart_id, $user_id]
                        );
                        
                        if (!$cartItem) {
                            throw new Exception("Cart item not found");
                        }
                        
                        // Verify stock availability
                        if ($quantity > $cartItem['stock']) {
                            throw new Exception("Cannot update quantity. Only {$cartItem['stock']} items in stock.");
                        }
                        
                        // Log attempt to update quantity
                        $GLOBALS['logger']->info('Updating cart item quantity', [
                            'user_id' => $user_id,
                            'cart_id' => $cart_id,
                            'old_quantity' => $cartItem['quantity'],
                            'new_quantity' => $quantity
                        ]);
                        
                        $db->execute(
                            "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?", 
                            [$quantity, $cart_id, $user_id]
                        );
                        $db->commit();
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    break;
                    
                case 'checkout':
                    // Redirect to checkout page
                    if (!empty($cartItems)) {
                        // Log checkout attempt
                        $GLOBALS['logger']->info('User proceeding to checkout', [
                            'user_id' => $user_id,
                            'cart_items' => count($cartItems),
                            'total_amount' => $totalPrice
                        ]);
                        
                        // Redirect to create order first
                        header('Location: /FYP/FYP/User/payment/checkout.php');
                    } else {
                        $error = "Your cart is empty.";
                    }
                    $db->rollback(); // No database changes for checkout action
                    exit;
            }
        } catch (Exception $e) {
            if (isset($db) && $db->isTransactionActive()) {
                $db->rollback();
            }
            $error = $e->getMessage();
            
            // Log the error
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->error('Cart operation error', [
                    'user_id' => $user_id,
                    'action' => $_POST['action'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

// Format price in MYR
function formatPrice($price) {
    // Convert the price to float to ensure compatibility with number_format
    $price = (float)$price;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="cart.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
<?php 
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    
include __DIR__ . '/../Header_and_Footer/header.php'; 
?>
    
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
                    <a href="/FYP/User/HomePage/homePage.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <?php foreach ($cartByStore as $storeName => $items): ?>
                        <div class="store">
                            <h3>
                                <input type="checkbox" checked> <?php echo htmlspecialchars($storeName); ?> Store
                            </h3>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="product">
                                    <img src="<?php echo '../../upload/' . htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
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
                                            <button type="button" class="remove-btn" data-cart-id="<?php echo $item['cart_id']; ?>">Remove</button>
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
                        <button type="button" id="checkout-btn" class="checkout-btn">Checkout</button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Add link to track orders -->
            <div style="margin: 20px 0; text-align: right;">
                <a href="track_order.php" class="btn btn-secondary">Track My Orders</a>
            </div>
        </div>
    </main>
    

    <form id="update-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" value="update_quantity">
        <input type="hidden" id="update-cart-id" name="cart_id">
        <input type="hidden" id="update-quantity" name="quantity">
    </form>
    
    <form id="remove-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" value="remove">
        <input type="hidden" id="remove-cart-id" name="cart_id">
    </form>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle quantity change
        const minusButtons = document.querySelectorAll('.quantity-btn.minus');
        const plusButtons = document.querySelectorAll('.quantity-btn.plus');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const removeButtons = document.querySelectorAll('.remove-btn');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        // Get CSRF token
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        
        minusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
                let value = parseInt(input.value, 10);
                value = Math.max(1, value - 1);
                input.value = value;
                
                // Update quantity via AJAX
                updateQuantity(cartId, value);
            });
        });
        
        plusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
                let value = parseInt(input.value, 10);
                value += 1;
                input.value = value;
                
                // Update quantity via AJAX
                updateQuantity(cartId, value);
            });
        });
        
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const cartId = this.name.match(/\[(\d+)\]/)[1];
                let value = parseInt(this.value, 10);
                value = Math.max(1, value); // Ensure minimum value is 1
                this.value = value;
                
                // Update quantity via AJAX
                updateQuantity(cartId, value);
            });
        });
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    const cartId = this.getAttribute('data-cart-id');
                    
                    // Set the cart ID in the hidden form and submit
                    document.getElementById('remove-cart-id').value = cartId;
                    document.getElementById('remove-form').submit();
                }
            });
        });
        
        // Handle checkout button click
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                // Show loading state
                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'Processing...';
                
                // Get shipping address (should be collected properly in a real application)
                const shippingAddress = '<?php echo addslashes(htmlspecialchars($user['address'] . ', ' . $user['city'] . ', ' . $user['postcode'] . ', ' . $user['state'])); ?>';
                
                // Create form data
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('shipping_address', shippingAddress);
                
                // Call the create order API
                fetch('/FYP/User/payment/create_order.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Redirect to login if unauthorized
                            window.location.href = '/FYP/User/login/login.php?redirect=' + encodeURIComponent(window.location.href);
                            throw new Error('Please login to complete your order');
                        } else if (response.status === 403) {
                            // Handle CSRF token errors by refreshing the page
                            window.location.reload();
                            throw new Error('Session expired. Please try again.');
                        }
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error processing request');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to checkout page
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.error || 'An error occurred while creating your order.');
                        // Reset button state
                        this.disabled = false;
                        this.textContent = originalText;
                    }
                })
                .catch(error => {
                    alert(error.message || 'Error processing your order. Please try again.');
                    console.error('Checkout error:', error);
                    
                    // Reset button state
                    this.disabled = false;
                    this.textContent = originalText;
                });
            });
        }
        
        function updateQuantity(cartId, quantity) {
            document.getElementById('update-cart-id').value = cartId;
            document.getElementById('update-quantity').value = quantity;
            document.getElementById('update-form').submit();
        }
    });
    </script>
</body>
</html>