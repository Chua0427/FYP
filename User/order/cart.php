<?php
declare(strict_types=1);
// Prevent browser caching of this dynamic page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/auth-check.php';

// Start session if not already started
ensure_session_started();

// Check if user is logged in
check_auth_redirect();

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
    
    // Remove cart entries for products marked as deleted
    $db->execute(
        "DELETE c FROM cart c JOIN product p ON c.product_id = p.product_id WHERE p.deleted = 1 AND c.user_id = ?",
        [$user_id]
    );
    
    // Fetch cart items for the logged-in user with product details
    $cartItems = $db->fetchAll(
        "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
         CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price,
         s.stock
         FROM cart c
         JOIN product p ON c.product_id = p.product_id
         JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
         WHERE c.user_id = ? AND p.deleted = 0
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
    <link rel="preload" href="critical.js" as="script">
    <script src="critical.js" defer></script>
    <?php add_auth_notification_resources(); ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
<?php 
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
                                                <input 
                                                    type="number" 
                                                    name="quantity[<?= htmlspecialchars((string)$item['cart_id']) ?>]" 
                                                    data-cart-id="<?= htmlspecialchars((string)$item['cart_id']) ?>" 
                                                    class="quantity-input" 
                                                    value="<?= htmlspecialchars((string)$item['quantity']) ?>" 
                                                    min="1" 
                                                    data-price="<?= htmlspecialchars((string)$item['final_price']) ?>" 
                                                    data-original-price="<?= htmlspecialchars((string)$item['price']) ?>"
                                                    data-max-stock="<?= htmlspecialchars((string)$item['stock']) ?>"
                                                >
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
                <a href="../View_Order/order.php" class="btn btn-secondary">Track My Orders</a>
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
    
    // Cart update tracking
    let isUpdating = false;
    let updateQueue = [];
    
    // Initialize stock data attributes
    quantityInputs.forEach(input => {
        input.setAttribute('data-last-valid-quantity', input.value);
        
        const productElement = input.closest('.product');
        if (productElement) {
            const stockText = productElement.querySelector('.product-variant') ? 
                            productElement.querySelector('.product-variant').textContent : '';
            const stockMatch = stockText.match(/\((\d+)\)/);
            if (stockMatch && stockMatch[1]) {
                input.setAttribute('data-max-stock', stockMatch[1]);
            }
        }
    });
    
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
            
            const productCheckboxes = storeDiv.querySelectorAll('.product-select');
            productCheckboxes.forEach(productCheckbox => {
                productCheckbox.checked = isChecked;
            });
            
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
            
            queueCartUpdate(cartId, value);
            updateCartTotals();
        });
    });
    
    plusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
            let value = parseInt(input.value, 10);
            
            const maxStock = parseInt(input.getAttribute('data-max-stock') || Number.MAX_SAFE_INTEGER, 10);
            
            if (value < maxStock) {
                value += 1;
                input.value = value;
                
                queueCartUpdate(cartId, value);
                updateCartTotals();
            } else {
                alert(`Stock limit reached. Only ${maxStock} items available.`);
            }
        });
    });
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.getAttribute('data-cart-id');
            let value = parseInt(this.value, 10);
            value = Math.max(1, value);
            
            const maxStock = parseInt(this.getAttribute('data-max-stock') || Number.MAX_SAFE_INTEGER, 10);
            
            if (value > maxStock) {
                value = maxStock;
                alert(`Stock limit reached. Only ${maxStock} items available.`);
            }
            
            this.value = value;
            
            queueCartUpdate(cartId, value);
            updateCartTotals();
        });
    });
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                const cartId = this.getAttribute('data-cart-id');
                removeItemAjax(cartId);
            }
        });
    });
    
    // Enhanced cart update function with queuing
    function queueCartUpdate(cartId, quantity) {
        // Add to queue
        updateQueue = updateQueue.filter(item => item.cartId !== cartId); // Remove existing updates for same item
        updateQueue.push({ cartId, quantity, timestamp: Date.now() });
        
        // Process queue if not currently updating
        if (!isUpdating) {
            processUpdateQueue();
        }
    }
    
    function processUpdateQueue() {
        if (updateQueue.length === 0 || isUpdating) {
            return;
        }
        
        isUpdating = true;
        const update = updateQueue.shift();
        
        updateQuantityAjax(update.cartId, update.quantity)
            .finally(() => {
                isUpdating = false;
                // Process next item in queue after a short delay
                setTimeout(() => {
                    if (updateQueue.length > 0) {
                        processUpdateQueue();
                    }
                }, 100);
            });
    }
    
    // Function to update cart totals based on selected items
    function updateCartTotals() {
        let totalItems = 0;
        let totalPrice = 0;
        let originalTotal = 0;
        
        const checkedItems = document.querySelectorAll('.product-select:checked');
        
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
        
        const discountTotal = originalTotal - totalPrice;
        
        const itemCountEl = document.getElementById('item-count');
        if (itemCountEl) itemCountEl.textContent = totalItems;

        const originalTotalEl = document.getElementById('original-total');
        if (originalTotalEl) originalTotalEl.textContent = formatCurrency(originalTotal);

        const discountEl = document.getElementById('total-discount');
        if (discountEl) discountEl.textContent = '-' + formatCurrency(discountTotal);

        const finalTotalEl = document.getElementById('total-price');
        if (finalTotalEl) finalTotalEl.textContent = formatCurrency(totalPrice);
        
        // Also sync the header cart badge
        const headerCountEl = document.getElementById('cartCount');
        if (headerCountEl) {
            headerCountEl.textContent = totalItems;
            headerCountEl.style.display = totalItems > 0 ? 'flex' : 'none';
        }
        
        // Enable/disable checkout button if present
        if (checkoutBtn) {
            if (checkedItems.length > 0) {
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = `Checkout `;
            } else {
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = 'Select items to checkout';
            }
        }
    }
    
    // AJAX function to update quantity
    function updateQuantityAjax(cartId, quantity) {
        const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
        
        // Show loading state
        input.classList.add('updating');
        
        // Use FormData to call the local update_cart.php endpoint
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('cart_id', cartId);
        formData.append('quantity', quantity);
        formData.append('ajax', '1');
        return fetch('update_cart.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(async response => {
            // Attempt to parse JSON body
            let data;
            try {
                data = await response.json();
            } catch (jsonErr) {
                throw new Error('Invalid JSON response from server');
            }
            // If HTTP status not OK, use server-provided error
            if (!response.ok) {
                const msg = data.error || 'Error updating cart';
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Update the price displays
                const productDiv = input.closest('.product');
                if (productDiv) {
                    const totalPriceElement = productDiv.querySelector('.product-total-price');
                    if (totalPriceElement) {
                        totalPriceElement.textContent = formatCurrency(data.item_total);
                    }
                }
                
                // Update stored quantity
                input.setAttribute('data-last-valid-quantity', quantity);
                
                // Update cart totals
                updateCartTotals();
                
                // Show success feedback
                showUpdateFeedback(input, 'success');
            } else {
                // Revert to last valid quantity on error
                const lastValidQuantity = input.getAttribute('data-last-valid-quantity');
                input.value = lastValidQuantity;
                showUpdateFeedback(input, 'error');
                
                // Show error message
                if (data.message) {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            
            // Revert to last valid quantity on error
            const lastValidQuantity = input.getAttribute('data-last-valid-quantity');
            input.value = lastValidQuantity;
            showUpdateFeedback(input, 'error');
            
            alert('Failed to update cart. Please try again.');
        })
        .finally(() => {
            input.classList.remove('updating');
        });
    }
    
    // AJAX function to remove item
    function removeItemAjax(cartId) {
        const productDiv = document.querySelector(`input[data-cart-id="${cartId}"]`).closest('.product');
        
        // Show loading state
        productDiv.style.opacity = '0.5';
        productDiv.style.pointerEvents = 'none';
        
        // Use FormData to call the local remove_cart_item.php endpoint
        const formDataRemove = new FormData();
        formDataRemove.append('csrf_token', csrfToken);
        formDataRemove.append('cart_id', cartId);
        fetch('remove_cart_item.php', {
            method: 'POST',
            body: formDataRemove,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the product element with animation
                productDiv.style.transition = 'all 0.3s ease';
                productDiv.style.transform = 'translateX(-100%)';
                productDiv.style.opacity = '0';
                
                setTimeout(() => {
                    // Remove product element
                    const storeDiv = productDiv.closest('.store');
                    productDiv.remove();
                    // If no more products in this store, remove its container
                    if (storeDiv && !storeDiv.querySelector('.product')) {
                        storeDiv.remove();
                    }
                    updateCartTotals();
                    
                    // If no more items at all, reload for empty cart
                    const remainingProducts = document.querySelectorAll('.product');
                    if (remainingProducts.length === 0) {
                        location.reload();
                    }
                }, 300);
            } else {
                // Restore state on error
                productDiv.style.opacity = '1';
                productDiv.style.pointerEvents = 'auto';
                
                if (data.message) {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
            
            // Restore state on error
            productDiv.style.opacity = '1';
            productDiv.style.pointerEvents = 'auto';
            
            alert('Failed to remove item. Please try again.');
        });
    }
    

    // Helper function to format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'MYR',
            minimumFractionDigits: 2
        }).format(amount);
    }
    
    // Checkout button functionality
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const selectedItems = [];
            const checkedBoxes = document.querySelectorAll('.product-select:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select items to checkout');
                return;
            }
            
            checkedBoxes.forEach(checkbox => {
                selectedItems.push(checkbox.value);
            });
            
            // Create a form to submit selected items
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../payment/checkout.php';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add selected items
            selectedItems.forEach(cartId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_items[]';
                input.value = cartId;
                form.appendChild(input);
            });
            
            // Show processing feedback
            this.textContent = 'Proceeding to checkout';
            this.disabled = true;
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // Handle browser back/forward button
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Page was restored from cache, update totals
            updateCartTotals();
        }
    });
    
    // Auto-save selected items to prevent loss on page refresh
    function saveSelectedItems() {
        const selected = [];
        document.querySelectorAll('.product-select:checked').forEach(checkbox => {
            selected.push(checkbox.value);
        });
        sessionStorage.setItem('selectedCartItems', JSON.stringify(selected));
    }
    
    function restoreSelectedItems() {
        const saved = sessionStorage.getItem('selectedCartItems');
        if (saved) {
            try {
                const selected = JSON.parse(saved);
                selected.forEach(cartId => {
                    const checkbox = document.querySelector(`.product-select[value="${cartId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                updateCartTotals();
            } catch (e) {
                console.error('Error restoring selected items:', e);
            }
        }
    }
    
    // Save selections on change
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', saveSelectedItems);
    });
    
    // Restore selections on page load
    restoreSelectedItems();
});
</script>
<script>
  // Reload page when restored from bfcache to fetch fresh data
  window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
      window.location.reload();
    }
  });
</script>
</body>
</html>