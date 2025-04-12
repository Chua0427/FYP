<?php

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';

use Ramsey\Uuid\Uuid;

session_start();

// Ensure logs directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Function to log messages
function log_message($level, $message) {
    $log = date("[Y-m-d H:i:s]") . " [$level] $message" . PHP_EOL;
    error_log($log, 3, __DIR__ . '/logs/order_management.log');
}

// Set content type for API responses
header('Content-Type: application/json');

try {
    // Initialize Database
    $db = new Database();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'User not logged in']);
        exit;
    }
    
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Determine the action to take
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create_order':
            createOrder($db, $user_id);
            break;
            
        case 'get_order_details':
            getOrderDetails($db);
            break;
            
        case 'update_order_status':
            updateOrderStatus($db);
            break;
            
        case 'get_user_orders':
            getUserOrders($db, $user_id);
            break;
            
        case 'get_all_orders':
            getAllOrders($db);
            break;
            
        default:
            throw new Exception("Invalid action: $action");
    }
} catch (Exception $e) {
    log_message('ERROR', "Order management error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    // Ensure database connection is closed
    if (isset($db)) {
        $db->close();
    }
}

/**
 * Create a new order from cart items
 * 
 * @param Database $db Database connection
 * @param int $user_id User ID
 * @return void
 */
function createOrder(Database $db, $user_id) {
    $db->beginTransaction();
    
    try {
        // Validate required parameters
        $shipping_address = trim($_POST['shipping_address'] ?? '');
        
        if (empty($shipping_address)) {
            throw new Exception('Shipping address is required');
        }
        
        // Check if the user has items in cart
        $cart_items = $db->fetchAll(
            "SELECT c.*, p.product_name, p.price, 
             CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 THEN p.discount_price ELSE p.price END as final_price 
             FROM cart c 
             JOIN product p ON c.product_id = p.product_id 
             WHERE c.user_id = ?", 
            [$user_id]
        );
        
        if (empty($cart_items)) {
            throw new Exception('Your cart is empty');
        }
        
        // Calculate order total
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += $item['final_price'] * $item['quantity'];
        }
        
        // Generate unique order ID
        $order_id = Uuid::uuid4()->toString();
        
        // Create order record
        $db->execute(
            "INSERT INTO orders (order_id, user_id, total_price, shipping_address, delivery_status) 
             VALUES (?, ?, ?, ?, 'prepare')",
            [$order_id, $user_id, $total_price, $shipping_address]
        );
        
        // Create order items from cart
        foreach ($cart_items as $item) {
            $db->execute(
                "INSERT INTO order_items (order_id, product_id, product_size, quantity, price) 
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $order_id, 
                    $item['product_id'], 
                    $item['product_size'], 
                    $item['quantity'], 
                    $item['final_price']
                ]
            );
            
            // Update stock (optional, based on your business logic)
            $db->execute(
                "UPDATE stock SET stock = stock - ? 
                 WHERE product_id = ? AND product_size = ?",
                [$item['quantity'], $item['product_id'], $item['product_size']]
            );
        }
        
        // Clear the user's cart
        $db->execute("DELETE FROM cart WHERE user_id = ?", [$user_id]);
        
        // Commit transaction
        $db->commit();
        
        // Log order creation
        log_message('INFO', "Order created: $order_id for user $user_id with total $total_price");
        
        // Return success response with order details
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'total' => $total_price,
            'message' => 'Order created successfully'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * Get order details with items and payment info
 * 
 * @param Database $db Database connection
 * @return void
 */
function getOrderDetails(Database $db) {
    // Validate required parameters
    $order_id = trim($_GET['order_id'] ?? '');
    
    if (empty($order_id)) {
        throw new Exception('Order ID is required');
    }
    
    // Get order info
    $order = $db->fetchOne(
        "SELECT * FROM orders WHERE order_id = ?", 
        [$order_id]
    );
    
    if (!$order) {
        throw new Exception("Order not found: $order_id");
    }
    
    // Get order items
    $items = $db->fetchAll(
        "SELECT oi.*, p.product_name, p.product_img1 
         FROM order_items oi 
         JOIN product p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?",
        [$order_id]
    );
    
    // Get payment information if exists
    $payment = $db->fetchOne(
        "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1", 
        [$order_id]
    );
    
    // Return order details
    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items,
        'payment' => $payment
    ]);
}

/**
 * Update order status
 * 
 * @param Database $db Database connection
 * @return void
 */
function updateOrderStatus(Database $db) {
    $db->beginTransaction();
    
    try {
        // Validate required parameters
        $order_id = trim($_POST['order_id'] ?? '');
        $status = trim($_POST['status'] ?? '');
        
        if (empty($order_id)) {
            throw new Exception('Order ID is required');
        }
        
        if (empty($status) || !in_array($status, ['prepare', 'packing', 'assign', 'shipped', 'delivered'])) {
            throw new Exception('Invalid status value');
        }
        
        // Check if order exists
        $order = $db->fetchOne(
            "SELECT * FROM orders WHERE order_id = ?", 
            [$order_id]
        );
        
        if (!$order) {
            throw new Exception("Order not found: $order_id");
        }
        
        // Update order status
        $db->execute(
            "UPDATE orders SET delivery_status = ? WHERE order_id = ?", 
            [$status, $order_id]
        );
        
        // Commit transaction
        $db->commit();
        
        // Log status update
        log_message('INFO', "Order $order_id status updated to $status");
        
        // Return success
        echo json_encode([
            'success' => true,
            'message' => "Order status updated to $status"
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * Get all orders for a user
 * 
 * @param Database $db Database connection
 * @param int $user_id User ID
 * @return void
 */
function getUserOrders(Database $db, $user_id) {
    // Get user's orders with summary info
    $orders = $db->fetchAll(
        "SELECT o.*, 
         (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count,
         (SELECT payment_status FROM payment WHERE order_id = o.order_id ORDER BY payment_at DESC LIMIT 1) as payment_status
         FROM orders o
         WHERE o.user_id = ?
         ORDER BY o.order_at DESC",
        [$user_id]
    );
    
    // Return orders
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
}

/**
 * Get all orders (admin function)
 * 
 * @param Database $db Database connection
 * @return void
 */
function getAllOrders(Database $db) {
    // Basic admin check
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        return;
    }
    
    // Optional filters
    $status = trim($_GET['status'] ?? '');
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    $query = "SELECT o.*, u.first_name, u.last_name, 
              (SELECT payment_status FROM payment WHERE order_id = o.order_id ORDER BY payment_at DESC LIMIT 1) as payment_status
              FROM orders o
              JOIN users u ON o.user_id = u.user_id";
    
    $params = [];
    
    if (!empty($status)) {
        $query .= " WHERE o.delivery_status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY o.order_at DESC LIMIT ?";
    $params[] = $limit;
    
    // Get filtered orders
    $orders = $db->fetchAll($query, $params);
    
    // Return orders
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
}
?>
