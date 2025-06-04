<?php
declare(strict_types=1);
// Include authentication check
require_once __DIR__ . '/../auth_check.php';
// Initialize session if not already started
if (isset($GLOBALS['session_started']) || session_status() === PHP_SESSION_ACTIVE) {
    // Session already started in init.php or elsewhere
} else if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../payment/db.php';

// Check if user is authenticated
Auth::requireAuth();
// Prevent browser caching to reflect updated review status on back navigation
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Set up logger for this page
$logger = $GLOBALS['logger'];

// Get user ID from session
$user_id = (int)$_SESSION['user_id'];

// Default filter values
$from_date = '';
$to_date = '';
$reference = '';
$sort_by = 'order_date_desc'; // Default sorting

// Process filter form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    $reference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'order_date_desc';
}

try {
    // Initialize database connection
    $db = new Database();
    
    // Build query with filters
    $query = "SELECT o.* FROM orders o WHERE o.user_id = ? AND o.delivery_status = 'delivered'";
    $params = [$user_id];
    
    // Add date filters if provided
    if (!empty($from_date)) {
        $query .= " AND o.order_at >= ?";
        $params[] = $from_date . " 00:00:00";
    }
    
    if (!empty($to_date)) {
        $query .= " AND o.order_at <= ?";
        $params[] = $to_date . " 23:59:59";
    }
    
    // Add reference filter if provided
    if (!empty($reference)) {
        // If the reference is purely numeric, treat it as an Order ID search
        if (ctype_digit($reference)) {
            $query .= " AND o.order_id = ?";
            $params[] = (int)$reference;
        } else {
            // Otherwise, search by product name
            $query .= " AND EXISTS (
                SELECT 1 FROM order_items oi
                JOIN product p ON oi.product_id = p.product_id
                WHERE oi.order_id = o.order_id
                  AND p.product_name LIKE ?
            )";
            $params[] = '%' . $reference . '%';
        }
    }
    
    // Add sorting based on user selection
    switch ($sort_by) {
        case 'order_date_asc':
            $query .= " ORDER BY o.order_at ASC";
            break;
        case 'order_date_desc':
            $query .= " ORDER BY o.order_at DESC";
            break;
        case 'order_id_asc':
            $query .= " ORDER BY o.order_id ASC";
            break;
        case 'order_id_desc':
            $query .= " ORDER BY o.order_id DESC";
            break;
        case 'total_price_asc':
            $query .= " ORDER BY o.total_price ASC";
            break;
        case 'total_price_desc':
            $query .= " ORDER BY o.total_price DESC";
            break;
        default:
            $query .= " ORDER BY o.order_at DESC";
            break;
    }
    
    // Fetch filtered orders
    $orders = $db->fetchAll($query, $params);

    // For each order, fetch order items and check if they've been reviewed
    foreach ($orders as &$order) {
        // Get order items with product details
        $order['items'] = $db->fetchAll(
            "SELECT oi.*, p.product_name, p.product_img1,
             (SELECT COUNT(*) FROM review WHERE user_id = ? AND product_id = oi.product_id AND order_id = ?) as reviewed
             FROM order_items oi
             JOIN product p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?",
            [$user_id, $order['order_id'], $order['order_id']]
        );
        
        // Count items needing review
        $order['needs_review_count'] = 0;
        foreach ($order['items'] as $item) {
            if ($item['reviewed'] == 0) {
                $order['needs_review_count']++;
            }
        }
    }
    unset($order);
    
    // Count total orders
    $total_orders = count($orders);
    
    // Count delivered orders (which is now the same as total orders)
    $delivered_orders = $total_orders;
    
} catch (Exception $e) {
    $logger->error('Error fetching order history', [
        'user_id' => $user_id,
        'error' => $e->getMessage()
    ]);
    
    $error_message = "An error occurred while fetching your order history. Please try again later.";
}

// Process add to cart if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    try {
        $product_id = (int)$_POST['product_id'];
        $size = $_POST['size'];
        $quantity = (int)$_POST['quantity'];

        // Verify product exists and not deleted
        $product = $db->fetchOne(
            "SELECT product_id, deleted FROM product WHERE product_id = ?",
            [$product_id]
        );
        if (!$product || (int)$product['deleted'] === 1) {
            // Product has been deleted or unavailable
            throw new Exception("Product is sold out");
        }
        // Check stock availability for this size
        $stockRow = $db->fetchOne(
            "SELECT stock FROM stock WHERE product_id = ? AND product_size = ?",
            [$product_id, $size]
        );
        if (!$stockRow || (int)$stockRow['stock'] < $quantity) {
            throw new Exception("Product is out of stock");
        }

        // Check for existing cart item with this user, product and size
        $cartItem = $db->fetchOne(
            "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND product_size = ?",
            [$user_id, $product_id, $size]
        );

        // Prevent exceeding available stock in cart
        $existingQty = $cartItem ? (int)$cartItem['quantity'] : 0;
        if ($existingQty + $quantity > (int)$stockRow['stock']) {
            // User already has all available stock in their cart
            throw new Exception("The product is full stock in cart");
        }

        if ($cartItem) {
            // Update quantity of existing cart item
            $db->execute(
                "UPDATE cart SET quantity = quantity + ?, added_at = CURRENT_TIMESTAMP WHERE cart_id = ? AND user_id = ?",
                [$quantity, $cartItem['cart_id'], $user_id]
            );
        } else {
            // Insert new cart item
            $db->execute(
                "INSERT INTO cart (user_id, product_id, product_size, quantity, added_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)",
                [$user_id, $product_id, $size, $quantity]
            );
        }

        // Log success
        $logger->info('Item added to cart from order history', [
            'user_id' => $user_id,
            'product_id' => $product_id,
            'product_size' => $size,
            'quantity' => $quantity
        ]);

        // Store message in session
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Product has been added to your cart.'
        ];
        
        // Redirect to prevent resubmission without parameters
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $logger->error('Error adding item to cart from order history', [
            'user_id' => $user_id,
            'product_id' => $product_id ?? null,
            'product_size' => $size ?? null,
            'error' => $e->getMessage()
        ]);
        // Store error in session
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => $e->getMessage()
        ];
    }
}

// Retrieve flash messages from session
$flash_message = null;
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    // Clear the message from session
    unset($_SESSION['flash_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="orderhistory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="order-history-container">
        <div class="header">
            <h1>Your Orders - <?php echo htmlspecialchars((string)$total_orders); ?></h1>
        </div>
        
        <?php if (isset($error_message) && !isset($flash_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($flash_message) && $flash_message['type'] === 'success'): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($flash_message['text']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($flash_message) && $flash_message['type'] === 'error'): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($flash_message['text']); ?>
            </div>
        <?php endif; ?>
        
        <div class="filter-section">
            <form method="post" class="filter-form" id="orderForm">
                <div class="filter-group">
                    <label for="from_date">From Date</label>
                    <div class="date-input-container">
                        <input type="text" id="from_date" name="from_date" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($from_date); ?>" autocomplete="off">
                        <i class="fas fa-calendar-alt calendar-icon" id="from_date_icon"></i>
                    </div>
                </div>
                <div class="filter-group">
                    <label for="to_date">To Date</label>
                    <div class="date-input-container">
                        <input type="text" id="to_date" name="to_date" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($to_date); ?>" autocomplete="off">
                        <i class="fas fa-calendar-alt calendar-icon" id="to_date_icon"></i>
                    </div>
                </div>
                <div class="filter-group">
                    <label for="reference">Reference</label>
                    <input type="text" id="reference" name="reference" placeholder="Enter Any Reference" value="<?php echo htmlspecialchars($reference); ?>">
                </div>
                <div class="filter-group">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" name="sort_by">
                        <option value="order_date_desc" <?php echo $sort_by === 'order_date_desc' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="order_date_asc" <?php echo $sort_by === 'order_date_asc' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="order_id_desc" <?php echo $sort_by === 'order_id_desc' ? 'selected' : ''; ?>>Order ID (High to Low)</option>
                        <option value="order_id_asc" <?php echo $sort_by === 'order_id_asc' ? 'selected' : ''; ?>>Order ID (Low to High)</option>
                        <option value="total_price_desc" <?php echo $sort_by === 'total_price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="total_price_asc" <?php echo $sort_by === 'total_price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="reset" class="filter-btn clear-btn" onclick="clearForm()">Clear</button>
                    <button type="submit" name="search" class="filter-btn search-btn">Search</button>
                </div>
            </form>
        </div>

        <?php if (!isset($error_message)): ?>
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>You haven't placed any orders yet.</p>
                    <a href="../All_Product_Page/all_product.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <label>Order Number :</label>
                                <span>#<?php echo htmlspecialchars((string)$order['order_id']); ?></span>
                            </div>
                            <div class="order-info">
                                <label>Order Date :</label>
                                <span><?php echo date('M j, Y', strtotime($order['order_at'])); ?></span>
                            </div>
                            <div class="order-info">
                                <label>Total Amount :</label>
                                <span>RM <?php echo number_format((float)$order['total_price'], 2, '.', ','); ?></span>
                            </div>
                            <div class="order-info">
                                <label>Status :</label>
                                <div>
                                    <span class="status status-<?php echo htmlspecialchars($order['delivery_status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['delivery_status'])); ?>
                                    </span>
                                    <?php if (isset($order['payment_status']) && $order['payment_status'] && $order['payment_status'] !== 'completed'): ?>
                                        <br>
                                        <span class="status payment-<?php echo htmlspecialchars($order['payment_status']); ?>">
                                            Payment: <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="order-actions">
                                <a href="../Delivery_Status_Page/delivery.php?id=<?php echo htmlspecialchars((string)$order['order_id']); ?>" class="btn btn-outline">Track Order</a>
                                <a href="view_payment_details.php?order_id=<?php echo htmlspecialchars((string)$order['order_id']); ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                        
                        <?php if ($order['needs_review_count'] > 0): ?>
                            <div class="needs-review">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $order['needs_review_count'] > 1 
                                    ? "You have " . htmlspecialchars((string)$order['needs_review_count']) . " items that need your review!" 
                                    : "You have 1 item that needs your review!"; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="product-item">
                                    <img src="../../upload/<?php echo htmlspecialchars($item['product_img1']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image">
                                    <div class="product-details">
                                        <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div class="product-specs">
                                            <span>Size : <strong><?php echo htmlspecialchars($item['product_size']); ?></strong></span>
                                            <span>Quantity : <strong><?php echo htmlspecialchars((string)$item['quantity']); ?></strong></span>
                                        </div>
                                    </div>
                                    <div class="product-price">RM <?php echo number_format((float)$item['price'], 2, '.', ','); ?></div>
                                    <div class="product-actions">
                                        <?php if ($item['reviewed'] == 0): ?>
                                            <a href="../Review_Page/write_review.php?product_id=<?php echo htmlspecialchars((string)$item['product_id']); ?>&order_id=<?php echo htmlspecialchars((string)$order['order_id']); ?>" class="btn btn-primary">
                                                Write Review
                                            </a>
                                        <?php else: ?>
                                            <span class="already-reviewed">
                                            Reviewed
                                            </span>
                                        <?php endif; ?>
                                        <form method="post" style="margin: 0;">
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars((string)$item['product_id']); ?>">
                                            <input type="hidden" name="size" value="<?php echo htmlspecialchars($item['product_size']); ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" name="add_to_cart" class="btn btn-outline">Buy Again</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function clearForm() {
            const form = document.getElementById('orderForm');
            form.reset();
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';
            document.getElementById('reference').value = '';
            document.getElementById('sort_by').value = 'order_date_desc';
            form.submit();
        }

        $(document).ready(function() {
            // Common datepicker options
            const datepickerOptions = {
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '2020:+0',
                showOtherMonths: true,
                selectOtherMonths: true
            };
            
            // Initialize datepickers
            $("#from_date").datepicker(datepickerOptions);
            $("#to_date").datepicker(datepickerOptions);
            
            // Add click event for calendar icons
            $("#from_date_icon").click(function() {
                $("#from_date").focus();
            });
            
            $("#to_date_icon").click(function() {
                $("#to_date").focus();
            });
            
            // Validate date format when manually typed
            function validateDateFormat(dateString) {
                if (!dateString) return true;
                // Check yyyy-mm-dd format
                const regex = /^\d{4}-\d{2}-\d{2}$/;
                if (!regex.test(dateString)) return false;
                
                // Check if it's a valid date
                const date = new Date(dateString);
                return !isNaN(date.getTime());
            }
            
            // Add form submit validation
            $("#orderForm").submit(function(e) {
                const fromDate = $("#from_date").val();
                const toDate = $("#to_date").val();
                
                if (fromDate && !validateDateFormat(fromDate)) {
                    alert("Please enter a valid From Date in YYYY-MM-DD format.");
                    e.preventDefault();
                    return false;
                }
                
                if (toDate && !validateDateFormat(toDate)) {
                    alert("Please enter a valid To Date in YYYY-MM-DD format.");
                    e.preventDefault();
                    return false;
                }
                
                if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                    alert("From Date cannot be later than To Date.");
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
        });
        
        // This function is used when updating cart count from Buy Again buttons
        function adjustCartCountSize() {
            const cartCount = document.getElementById('cartCount');
            if (!cartCount) return;
            
            const count = cartCount.textContent.trim();
            if (count.length >= 3) {
                // For 3 or more digits (100+)
                cartCount.style.fontSize = '8px';
                cartCount.style.width = '22px';
            } else if (count.length === 2) {
                // For 2 digits (10-99)
                cartCount.style.fontSize = '10px';
                cartCount.style.width = '20px';
            } else {
                // For 1 digit (0-9)
                cartCount.style.fontSize = '12px';
                cartCount.style.width = '20px';
            }
            
            // Ensure vertical alignment
            cartCount.style.lineHeight = '1';
            cartCount.style.display = 'flex';
            cartCount.style.alignItems = 'center';
            cartCount.style.justifyContent = 'center';
        }
        
        // Function to update cart count in the header
        function updateHeaderCartCount(count) {
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                if (count > 0) {
                    cartCount.textContent = count;
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.textContent = '0';
                    cartCount.style.display = 'none';
                }
                adjustCartCountSize();
            }
        }

        // Auto-hide flash messages after 5 seconds and refresh page without params
        $(function() {
            var msgs = $('.success-message, .error-message');
            if (msgs.length) {
                // Fade out messages
                setTimeout(function() {
                    msgs.fadeOut('slow');
                    
                    // Refresh the page after fade animation completes
                }, 3000);
            }
        });
    </script>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>