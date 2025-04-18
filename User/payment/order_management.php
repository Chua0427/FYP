<?php
declare(strict_types=1);
ob_clean();

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/secrets.php';
require_once __DIR__ . '/db.php';
require __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/services/OrderService.php';

// Remove UUID import since we're now using auto-increment IDs


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
    exit;
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
    try {
        // Validate required parameters
        $shipping_address = trim($_POST['shipping_address'] ?? '');
        
        // Create OrderService instance and use it to create the order
        $orderService = new OrderService($db);
        
        // For older code compatibility, we use a custom logger function
        $orderService->setLogger(new class {
            public function info($message, $context = []) {
                log_message('INFO', $message . ' | ' . json_encode($context));
            }
            
            public function error($message, $context = []) {
                log_message('ERROR', $message . ' | ' . json_encode($context));
            }
        });
        
        // Create order without strict stock validation (simpler cart query)
        $result = $orderService->createOrderFromCart(
            $user_id,
            $shipping_address,
            false // Don't check stock in this implementation
        );
        
        // Return success response with order details
        echo json_encode($result);
        exit;
        
    } catch (Exception $e) {
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
    try {
        // Validate required parameters
        $order_id = trim($_GET['order_id'] ?? '');
        
        if (empty($order_id)) {
            throw new Exception('Order ID is required');
        }
        
        // Create OrderService instance
        $orderService = new OrderService($db);
        
        // Get order details
        $details = $orderService->getOrderDetails((int)$order_id);
        
        // Return order details
        echo json_encode([
            'success' => true,
            'order' => $details['order'],
            'items' => $details['items'],
            'payment' => $details['payment']
        ]);
        exit;
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Update order status
 * 
 * @param Database $db Database connection
 * @return void
 */
function updateOrderStatus(Database $db) {
    try {
        // Validate required parameters
        $order_id = trim($_POST['order_id'] ?? '');
        $status = trim($_POST['status'] ?? '');
        
        if (empty($order_id)) {
            throw new Exception('Order ID is required');
        }
        
        // Create OrderService instance
        $orderService = new OrderService($db);
        
        // For older code compatibility, we use a custom logger function
        $orderService->setLogger(new class {
            public function info($message, $context = []) {
                log_message('INFO', $message . ' | ' . json_encode($context));
            }
            
            public function error($message, $context = []) {
                log_message('ERROR', $message . ' | ' . json_encode($context));
            }
        });
        
        // Update order status
        $orderService->updateOrderStatus((int)$order_id, $status);
        
        // Return success
        echo json_encode([
            'success' => true,
            'message' => "Order status updated to $status"
        ]);
        exit;
        
    } catch (Exception $e) {
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
    exit;
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
        exit;
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
    exit;
}
?>
