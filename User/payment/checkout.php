<?php
declare(strict_types=1);
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/csrf.php';

// Include authentication check
require_once __DIR__ . '/../auth_check.php';

// Initialize session if not already started
ensure_session_started();


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /FYP/User/login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}


$user_id = $_SESSION['user_id'];
$error = null;
$success = null;
$csrf_token = generateCsrfToken();


// Debug log function for stock updates
function debug_log($message, $data = []) {
    $log_file = __DIR__ . '/logs/stock_update_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
   
    if (!empty($data)) {
        $log_message .= " - " . json_encode($data);
    }
   
    $log_message .= PHP_EOL;
   
    // Create logs directory if it doesn't exist
    $log_dir = dirname($log_file);
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
   
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}


try {
    // Initialize Database
    $db = new Database();
   
    // Remove cart entries for products marked as deleted
    $db->execute(
        "DELETE c FROM cart c JOIN product p ON c.product_id = p.product_id WHERE p.deleted = 1 AND c.user_id = ?",
        [$user_id]
    );
   
    // Remove unchecked items based on selected products
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items']) && !isset($_POST['create_order'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Invalid security token. Please try again.");
        }

        // Sanitize selected items
        $selectedItems = array_map('intval', $_POST['selected_items']);
        if (empty($selectedItems)) {
            throw new Exception("No items selected for checkout.");
        }

        // Store selected cart IDs in session to maintain selection through the checkout flow
        $_SESSION['selected_cart_items'] = $selectedItems;
        
        // Log selected items
        if (isset($GLOBALS['logger'])) {
            $GLOBALS['logger']->info('User selected items for checkout', [
                'user_id' => $user_id, 
                'selected_items' => $selectedItems
            ]);
        }
    }
    
    // Check if we have previously selected items in session
    if (isset($_SESSION['selected_cart_items'])) {
        $selectedItems = $_SESSION['selected_cart_items'];
    } else {
        // If no selection in session, select all cart items (fallback)
        $allCartItems = $db->fetchAll(
            "SELECT cart_id FROM cart WHERE user_id = ?",
            [$user_id]
        );
        $selectedItems = array_map(function($item) { 
            return (int)$item['cart_id']; 
        }, $allCartItems);
        
        // Store in session
        $_SESSION['selected_cart_items'] = $selectedItems;
    }
    
    // Clear any previous checkout data to ensure fresh totals
    unset($_SESSION['checkout_cart_items'], $_SESSION['checkout_total_price']);
    
    // Fetch fresh cart items - only selected ones
    $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
    $params = array_merge([$user_id], $selectedItems);
    
    if (!empty($selectedItems)) {
        $cartItems = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.discount_price, p.product_img1, p.brand,
             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price,
             s.stock
             FROM cart c
             JOIN product p ON c.product_id = p.product_id
             JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
             WHERE c.user_id = ? AND c.cart_id IN ($placeholders) AND p.deleted = 0
             ORDER BY c.added_at DESC",
            $params
        );
    } else {
        $cartItems = [];
    }
    
    if (empty($cartItems)) {
        throw new Exception("No items selected for checkout. Please select items from your cart.");
    }
   
    // Get user details for shipping
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE user_id = ?",
        [$user_id]
    );
   
    if (!$user) {
        throw new Exception("User information not found");
    }
   
    // Process form submission - Collect shipping address and continue to payment methods
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order']) && isset($_POST['csrf_token'])) {
        // Validate CSRF token
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Invalid form submission. Please try again.");
        }


        // Validate shipping address
        $shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';
        if (empty($shipping_address)) {
            throw new Exception("Shipping address is required");
        }


        // Store shipping address in session for use in payment process
        $_SESSION['checkout_shipping_address'] = $shipping_address;
       
        // Calculate cart total for display on payment page
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['final_price'] * $item['quantity'];
        }
        $_SESSION['checkout_total_price'] = $totalPrice;
       
        // Store selected cart items in session
        $_SESSION['checkout_cart_items'] = $cartItems;
       
        // Log checkout step
        if (isset($GLOBALS['logger'])) {
            $GLOBALS['logger']->info('User proceeded to payment selection', [
                'user_id' => $user_id
            ]);
        }
       
        // Redirect to payment selection page
        header("Location: payment_methods.php");
        exit;
    }
   
} catch (Exception $e) {
    $error = $e->getMessage();
   
    // Log error
    error_log("Checkout error: " . $e->getMessage());
}


// Function to format price
function formatPrice($price) {
    return 'MYR ' . number_format((float)$price, 2);
}


// Prepare cart data for display - Fix the display issue
$items = $cartItems ?? [];
$totalItems = 0;
$totalPrice = 0;
$totalOriginalPrice = 0;


foreach ($items as $item) {
    $totalItems += $item['quantity'];
    $totalPrice += $item['final_price'] * $item['quantity'];
    $totalOriginalPrice += $item['price'] * $item['quantity'];
}


$totalDiscount = $totalOriginalPrice - $totalPrice;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">


    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
    <div class="page-container">
        <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
       
        <main>
            <div class="checkout-container">
                <h1>Checkout</h1>
               
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                        <p style="margin-top: 20px;">
                            <?php if (stripos($error, "cart is empty") !== false): ?>
                                <a href="../HomePage/homePage.php" class="btn btn-primary">Continue Shopping</a>
                            <?php else: ?>
                                <a href="../order/cart.php" class="return-link">Return to Cart</a>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php elseif ($success): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php else: ?>
                    <form method="post" id="checkout-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateCheckoutForm(this);">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                       
                        <div class="checkout-grid">
                            <div class="checkout-details">
                                <section class="shipping-section">
                                    <h2>Shipping Information</h2>
                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>" readonly>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="shipping_address">Shipping Address</label>
                                        <textarea id="shipping_address" name="shipping_address" required><?php
                                            $address_parts = array_filter([
                                                $user['address'] ?? '',
                                                $user['city'] ?? '',
                                                $user['postcode'] ?? '',
                                                $user['state'] ?? ''
                                            ]);
                                            echo htmlspecialchars(implode(', ', $address_parts));
                                        ?></textarea>
                                        <p class="help-text">You can edit your shipping address if needed</p>
                                    </div>
                                </section>
                               
                                <section class="payment-section">
                                    <h2>Next Step: Payment method</h2>
                                    <p>After confirming your order, you will be directed to select your payment method.</p>
                                </section>
                               
                                <div class="button-container">
                                    <button type="submit" name="create_order" class="btn btn-primary">Continue to Payment</button>
                                    <a href="../order/cart.php" class="return-link">Return to Cart</a>
                                </div>
                            </div>
                           
                            <div class="order-summary">
                                <h2>Order Summary</h2>
                               
                                <div id="order-items-container">
                                    <?php if (!empty($items)): ?>
                                        <?php foreach ($items as $item): ?>
                                            <div class="order-item" data-cart-id="<?php echo htmlspecialchars((string)$item['cart_id']); ?>">
                                                <div class="item-details">
                                                    <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                                    <p class="item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                                    <p class="item-quantity">Size: <?php echo htmlspecialchars($item['product_size']); ?> | Qty: <?php echo htmlspecialchars((string)$item['quantity']); ?></p>
                                                </div>
                                                <div class="item-price">
                                                    <?php
                                                    $itemPrice = $item['final_price'] * $item['quantity'];
                                                    echo formatPrice($itemPrice);
                                                    ?>
                                                    <?php if (isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']): ?>
                                                        <div class="original-price">
                                                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-cart-message">
                                            <p>No items in cart</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                               
                                <div class="summary-totals">
                                    <div class="summary-row">
                                        <span>Subtotal (<span id="checkout-item-count"><?php echo $totalItems; ?></span> items)</span>
                                        <span id="checkout-subtotal"><?php echo formatPrice($totalOriginalPrice); ?></span>
                                    </div>
                                    <?php if ($totalDiscount > 0): ?>
                                    <div class="summary-row discount">
                                        <span>Total Discount</span>
                                        <span id="checkout-discount">-<?php echo formatPrice($totalDiscount); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="summary-row">
                                        <span>Sales Tax (6% SST)</span>
                                        <span>Included</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Shipping</span>
                                        <span>Free</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span>Total</span>
                                        <span id="checkout-total"><?php echo formatPrice($totalPrice); ?></span>
                                    </div>
                                    <div class="billing-note">
                                        <p>* All prices are in Malaysian Ringgit (MYR)</p>
                                        <p>* 6% Sales and Service Tax (SST) is included in all listed prices</p>
                                        <p>* Free shipping for all domestic orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
       
        <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
    </div>
   
    <script>
      // Enhanced cart data fetching with robust error handling
      function fetchCheckoutSummary() {
        fetch('../api/get_cart_data.php?checkout=1', {
          credentials: 'same-origin',
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache'
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data && data.success) {
            updateCheckoutDisplay(data);
          } else {
            console.error('Failed to fetch cart data:', data?.error || 'Unknown error');
            showErrorMessage('Failed to update cart summary. Please refresh the page.');
          }
        })
        .catch(error => {
          console.error('Error fetching cart summary:', error);
          showErrorMessage('Network error while updating cart. Please refresh the page.');
        });
      }


      function updateCheckoutDisplay(data) {
        // Ensure we have valid data
        if (!data || !data.items || !data.summary) {
          console.error('Invalid cart data structure received');
          return;
        }


        const items = data.items || [];
        const summary = data.summary || {};
       
        // Destructure summary fields with defaults
        const totalItems = typeof summary.totalItems === 'number' ? summary.totalItems : 0;
        const totalOriginalPrice = typeof summary.totalOriginalPrice === 'number' ? summary.totalOriginalPrice : 0;
        const totalDiscount = typeof summary.totalDiscount === 'number' ? summary.totalDiscount : 0;
        const totalPrice = typeof summary.totalPrice === 'number' ? summary.totalPrice : 0;
       
        // Update cart count in localStorage for header synchronization
        localStorage.setItem('cartCount', totalItems);
        // Also update the header cart count if the function exists
        if (typeof updateCartCountDisplay === 'function') {
          updateCartCountDisplay(totalItems);
        }
       
        // Update order items container
        const itemsContainer = document.getElementById('order-items-container');
        if (!itemsContainer) {
          console.error('Order items container not found');
          return;
        }


        if (items.length > 0) {
          itemsContainer.innerHTML = items.map(item => `
            <div class="order-item" data-cart-id="${item.cart_id}">
              <div class="item-details">
                <p class="item-name">${item.product_name}</p>
                <p class="item-brand">${item.brand}</p>
                <p class="item-quantity">Size: ${item.product_size} | Qty: ${item.quantity}</p>
              </div>
              <div class="item-price">
                MYR ${(item.item_total).toFixed(2)}
                ${item.discount_price && item.discount_price < item.price ? `
                  <div class="original-price">MYR ${(item.item_original_total).toFixed(2)}</div>
                ` : ''}
              </div>
            </div>
          `).join('');
        } else {
          itemsContainer.innerHTML = '<div class="empty-cart-message"><p>Your cart is empty. Please add items before checkout.</p><p><a href="../HomePage/index.php" class="return-link">Continue Shopping</a></p></div>';
         
          // Also disable the checkout button if cart is empty
          const checkoutButton = document.querySelector('button[name="create_order"]');
          if (checkoutButton) {
            checkoutButton.disabled = true;
          }
        }
       
        // Update summary totals
        const countEl = document.getElementById('checkout-item-count');
        const subtotalEl = document.getElementById('checkout-subtotal');
        const discountEl = document.getElementById('checkout-discount');
        const discountRow = discountEl ? discountEl.closest('.summary-row') : null;
        const totalEl = document.getElementById('checkout-total');
       
        if (countEl) countEl.textContent = totalItems;
        if (subtotalEl) subtotalEl.textContent = `MYR ${totalOriginalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
       
        if (discountEl && discountRow) {
          if (totalDiscount > 0) {
            discountEl.textContent = `-MYR ${totalDiscount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            discountRow.style.display = 'flex';
          } else {
            discountRow.style.display = 'none';
          }
        }
       
        if (totalEl) totalEl.textContent = `MYR ${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
      }


      function showErrorMessage(message) {
        // Create or update error message display
        let errorDiv = document.getElementById('checkout-error-message');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.id = 'checkout-error-message';
          errorDiv.className = 'error-message';
          errorDiv.style.cssText = 'margin: 10px 0; padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;';
         
          const container = document.querySelector('.checkout-container');
          if (container) {
            container.insertBefore(errorDiv, container.firstChild.nextSibling);
          }
        }
       
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
       
        // Auto-hide after 5 seconds
        setTimeout(() => {
          if (errorDiv) {
            errorDiv.style.display = 'none';
          }
        }, 5000);
      }


      // Listen for cart updates from other tabs/windows
      window.addEventListener('storage', function(e) {
        if (e.key === 'cartUpdated') {
          fetchCheckoutSummary();
        }
      });


      // Refresh cart data when page becomes visible again or after navigation
      window.addEventListener('pageshow', function(e) {
        // Always refresh on pageshow as this catches both loads and back/forward navigation
        fetchCheckoutSummary();
       
        // Clear the localStorage flag
        if (localStorage.getItem('cartUpdated')) {
          localStorage.removeItem('cartUpdated');
        }
      });


      // Initial load
      document.addEventListener('DOMContentLoaded', function() {
        // Always fetch cart data, even if the server-side HTML shows empty
        // This ensures we have the latest data and corrects any session inconsistencies
        fetchCheckoutSummary();
      });


      // Basic form validation and button feedback
      function validateCheckoutForm(form) {
        const shippingAddress = document.getElementById('shipping_address');
        if (!shippingAddress || !shippingAddress.value.trim()) {
          showErrorMessage('Please enter a shipping address');
          return false;
        }
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {

          submitBtn.textContent = 'Processing...';
        }
        return true;
      
      }
    </script>
</body>
</html>

