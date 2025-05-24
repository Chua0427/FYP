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
        $query .= " AND o.order_id = ?";
        $params[] = $reference;
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

        // Verify product exists
        $product = $db->fetchOne(
            "SELECT product_id FROM product WHERE product_id = ?",
            [$product_id]
        );
        if (!$product) {
            throw new Exception("Product not found");
        }

        // Check for existing cart item with this user, product and size
        $cartItem = $db->fetchOne(
            "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND product_size = ?",
            [$user_id, $product_id, $size]
        );

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

        // Redirect to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?cart_added=1");
        exit;
    } catch (Exception $e) {
        $logger->error('Error adding item to cart from order history', [
            'user_id' => $user_id,
            'product_id' => $product_id ?? null,
            'product_size' => $size ?? null,
            'error' => $e->getMessage()
        ]);
        $cart_error = "Failed to add item to cart: " . $e->getMessage();
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .order-history-container {
            max-width: 900px;
            position: relative;
            top: 100px;
            margin: auto auto 150px auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        .filter-group input, 
        .filter-group select {
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 14px;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
        }
        
        .filter-btn {
            height: 40px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            border: 1px solid;
            transition: all 0.2s ease;
            padding: 0 20px;
        }
        
        .clear-btn {
            background: white;
            color: #666;
            border-color: #ddd;
        }
        
        .clear-btn:hover {
            background: #f8f9fa;
        }
        
        .search-btn {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }
        
        .search-btn:hover {
            background: #4338ca;
            border-color: #4338ca;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .order-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: grid;
            grid-template-columns: repeat(4, 1fr) auto;
            gap: 20px;
            align-items: center;
        }

        .order-info {
            display: flex;
            flex-direction: column;
        }

        .order-info label {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .order-info span {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 600;
        }

        .status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 4px;
            display: inline-block;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-prepare {
            background: #ffeeba;
            color: #856404;
        }

        .status-packing {
            background: #b8daff;
            color: #004085;
        }

        .status-assign {
            background: #c3e6cb;
            color: #155724;
        }

        .payment-pending {
            background: #ffeeba;
            color: #856404;
        }

        .payment-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-refunded {
            background: #d1ecf1;
            color: #0c5460;
        }

        .order-actions {
            display: flex;
            gap: 8px;
            flex-direction: column;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            border: 1px solid;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-outline {
            background: white;
            color: #666;
            border-color: #ddd;
        }

        .btn-outline:hover {
            background: #f8f9fa;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background: #4338ca;
            border-color: #4338ca;
        }

        .needs-review {
            padding: 16px 20px;
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            margin: 0 20px;
            border-radius: 0 6px 6px 0;
            font-size: 14px;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .needs-review i {
            font-size: 16px;
        }

        .product-list {
            padding: 20px;
        }

        .product-item {
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            background: #f0f0f0;
            border: 1px solid #eee;
        }

        .product-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .product-specs {
            display: flex;
            gap: 16px;
            font-size: 12px;
            color: #666;
        }

        .product-specs span {
            font-weight: normal;
        }

        .product-specs strong {
            color: #333;
        }

        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-right: 16px;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .product-actions .btn {
            font-size: 11px;
            padding: 6px 12px;
            min-width: 100px;
        }

        .already-reviewed {
            color: #28a745;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .order-footer {
            background: #f9f9f9;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f0f0f0;
        }

        .order-footer-actions {
            display: flex;
            gap: 16px;
        }

        .order-footer-actions a {
            color: #4f46e5;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .order-footer-actions a:hover {
            text-decoration: underline;
        }

        .no-orders {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-orders i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-orders p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .error-message, .success-message {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                grid-column: 1 / -1;
            }
            
            .order-header {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .order-actions {
                flex-direction: row;
                justify-content: flex-start;
            }

            .product-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-image {
                width: 100%;
                height: 120px;
            }

            .product-actions {
                align-items: flex-start;
                flex-direction: row;
                width: 100%;
                justify-content: space-between;
            }

            .product-price {
                margin-right: 0;
                align-self: flex-end;
                margin-top: -30px;
            }

            .order-footer {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .order-footer-actions {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="order-history-container">
        <div class="header">
            <h1>Your Orders - <?php echo htmlspecialchars((string)$total_orders); ?></h1>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['cart_added']) && $_GET['cart_added'] == 1): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                Product has been added to your cart.
            </div>
        <?php endif; ?>
        
        <?php if (isset($cart_error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($cart_error); ?>
            </div>
        <?php endif; ?>
        
        <div class="filter-section">
            <form method="post" class="filter-form" id="orderForm">
                <div class="filter-group">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
                </div>
                <div class="filter-group">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
                </div>
                <div class="filter-group">
                    <label for="reference">Reference</label>
                    <input type="text" id="reference" name="reference" placeholder="#" value="<?php echo htmlspecialchars($reference); ?>">
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
                                                <i class="fas fa-check-circle"></i> Reviewed
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
            document.getElementById('orderForm').reset();
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';
            document.getElementById('reference').value = '';
            document.getElementById('sort_by').value = 'order_date_desc';
        }
    </script>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>