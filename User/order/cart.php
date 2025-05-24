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
                    <a href="/FYP//FYP/User/HomePage/homePage.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <?php foreach ($cartByStore as $storeName => $items): ?>
                        <div class="store">
                            <h3>
                                <input type="checkbox" checked>
                                <a href="../All_Product_Page/all_product.php?brand=<?php echo urlencode($storeName); ?>" class="store-link">
                                    <?php echo htmlspecialchars($storeName); ?>
                                </a>
                            </h3>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="product">
                                    <input type="checkbox" class="product-select" name="selected_items[]" value="<?php echo $item['cart_id']; ?>" checked data-price="<?php echo $item['final_price'] * $item['quantity']; ?>">
                                    <a href="../ProductPage/product.php?id=<?php echo $item['product_id']; ?>" class="product-link">
                                        <img src="<?php echo '../../upload/' . htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    </a>
                                    <div class="product-details">
                                        <a href="../ProductPage/product.php?id=<?php echo $item['product_id']; ?>" class="product-name-link">
                                            <p class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        </a>
                                        <p class="product-variant">Size: <?php echo htmlspecialchars($item['product_size']); ?></p>
                                        <div class="price">
                                            <?php echo formatPrice($item['final_price']); ?>
                                            <?php if (isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']): ?>
                                                <del><?php echo formatPrice($item['price']); ?></del>
                                            <?php endif; ?>
                                        </div>
                                        <div class="actions">
                                            <div class="quantity-control">
                                                <button type="button" class="quantity-btn minus" data-cart-id="<?php echo $item['cart_id']; ?>">-</button>
                                                <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['final_price']; ?>" data-original-price="<?php echo $item['price']; ?>">
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
                            <span>Total Items (<span id="item-count"><?php echo $totalItems; ?></span>)</span>
                            <span id="original-total"><?php echo formatPrice($totalOriginalPrice); ?></span>
                        </div>
                        <?php if ($totalDiscount > 0): ?>
                        <div class="row">
                            <span>Total Discount</span>
                            <span id="total-discount">-<?php echo formatPrice($totalDiscount); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <span>Sales Tax (6% SST)</span>
                            <span>Included</span>
                        </div>
                        <div class="row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="row" style="font-weight: bold; font-size: 1.1rem;">
                            <span>Total</span>
                            <span id="total-price"><?php echo formatPrice($totalPrice); ?></span>
                        </div>
                        <div class="billing-info">
                            <p>* All prices are in Malaysian Ringgit (MYR)</p>
                            <p>* Sales tax (6% SST) is included in the price</p>
                            <p>* Free shipping for all domestic orders</p>
                        </div>
                        <button type="button" id="checkout-btn" class="checkout-btn">Checkout</button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Add link to track orders -->
            <div style="margin: 20px 0; text-align: right;">
                <a href="../View_Order/view_order.php" class="btn btn-secondary">Track My Orders</a>
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
        const productCheckboxes = document.querySelectorAll('.product-select');
        const storeCheckboxes = document.querySelectorAll('.store h3 input[type="checkbox"]');
        
        // Get CSRF token
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        
        // Initialize the cart calculation
        updateCartTotals();
        
        // Event listener for product checkboxes
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCartTotals();
            });
        });
        
        // Event listener for store checkboxes
        storeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const storeDiv = this.closest('.store');
                const isChecked = this.checked;
                
                // Update all product checkboxes in this store
                const productCheckboxes = storeDiv.querySelectorAll('.product-select');
                productCheckboxes.forEach(productCheckbox => {
                    productCheckbox.checked = isChecked;
                });
                
                // Update cart totals
                updateCartTotals();
            });
        });
        
        minusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
                let value = parseInt(input.value, 10);
                value = Math.max(1, value - 1);
                input.value = value;
                
                // Update quantity via AJAX
                updateQuantityAjax(cartId, value);
                
                // Update cart totals
                updateCartTotals();
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
                updateQuantityAjax(cartId, value);
                
                // Update cart totals
                updateCartTotals();
            });
        });
        
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const cartId = this.getAttribute('data-cart-id');
                let value = parseInt(this.value, 10);
                value = Math.max(1, value); // Ensure minimum value is 1
                this.value = value;
                
                // Update quantity via AJAX
                updateQuantityAjax(cartId, value);
                
                // Update cart totals
                updateCartTotals();
            });
        });
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    const cartId = this.getAttribute('data-cart-id');
                    
                    // Remove item via AJAX
                    removeItemAjax(cartId);
                }
            });
        });
        
        // Function to update cart totals based on selected items
        function updateCartTotals() {
            let totalItems = 0;
            let totalPrice = 0;
            let originalTotal = 0;
            
            // Get all checked product checkboxes
            const checkedItems = document.querySelectorAll('.product-select:checked');
            
            // Calculate totals based on checked items
            checkedItems.forEach(checkbox => {
                const cartId = checkbox.value;
                const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
                const finalUnit = parseFloat(input.getAttribute('data-price'));
                const origUnit = parseFloat(input.getAttribute('data-original-price'));
                const qty = parseInt(input.value, 10);
                
                totalItems += qty;
                totalPrice += finalUnit * qty;
                originalTotal += origUnit * qty;
            });
            
            // Compute discount
            const discountTotal = originalTotal - totalPrice;
            
            // Update the displayed totals
            document.getElementById('item-count').textContent = totalItems;
            document.getElementById('original-total').textContent = 'RM ' + originalTotal.toFixed(2);
            if (document.getElementById('total-discount')) {
                document.getElementById('total-discount').textContent = '-' + 'RM ' + discountTotal.toFixed(2);
            }
            document.getElementById('total-price').textContent = 'RM ' + totalPrice.toFixed(2);
        }
        
        // Function to update quantity via AJAX
        function updateQuantityAjax(cartId, quantity) {
            // Create form data
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('cart_id', cartId);
            formData.append('quantity', quantity);
            formData.append('ajax', '1');
            
            // Show loading indicator
            // You could add a loading spinner here
            
            // Send AJAX request
            fetch('update_cart.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        // Redirect to login if unauthorized
                        window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                        throw new Error('Please login to update your cart');
                    }
                    return response.json().then(err => {
                        throw new Error(err.error || 'Error updating cart');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    alert(data.error || 'An error occurred while updating your cart.');
                    // Reset the quantity to the original value if needed
                }
            })
            .catch(error => {
                alert(error.message || 'Error updating your cart. Please try again.');
                console.error('Update cart error:', error);
            });
        }
        
        // Function to remove item via AJAX
        function removeItemAjax(cartId) {
            // Create form data
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('cart_id', cartId);
            formData.append('ajax', '1');
            
            // Send AJAX request
            fetch('remove_cart_item.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Error removing item');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove the item from the DOM
                    const productElement = document.querySelector(`.product-select[value="${cartId}"]`).closest('.product');
                    productElement.remove();
                    
                    // Update cart totals
                    updateCartTotals();
                    
                    // If no more items in cart, reload page to show empty cart message
                    const remainingItems = document.querySelectorAll('.product').length;
                    if (remainingItems === 0) {
                        location.reload();
                    }
                } else {
                    alert(data.error || 'An error occurred while removing the item.');
                }
            })
            .catch(error => {
                alert(error.message || 'Error removing item. Please try again.');
                console.error('Remove item error:', error);
            });
        }
        
        // Handle checkout button click
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                // Ensure at least one item is selected
                const selectedItems = document.querySelectorAll('.product-select:checked');
                if (selectedItems.length === 0) {
                    alert('Please select at least one item to checkout.');
                    return;
                }

                // Show loading state
                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'Processing...';

                // Redirect to checkout page for shipping info
                window.location.href = '/FYP/FYP/User/payment/checkout.php';
            });
        }
    });
    </script>
</body>
</html>