<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';

/**
 * Order Service class for handling order-related operations
 */
class OrderService
{
    /**
     * @var Database Database connection
     */
    private Database $db;
    
    /**
     * @var object Logger instance
     */
    private $logger;
    
    /**
     * Constructor
     * 
     * @param Database $db Database connection
     * @param object $logger Logger instance
     */
    public function __construct(Database $db, $logger = null)
    {
        $this->db = $db;
        $this->logger = $logger;
    }
    
    /**
     * Set logger instance
     * 
     * @param object $logger Any object with logging methods (info, error, etc.)
     * @return self
     */
    public function setLogger($logger): self
    {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Log a message if logger is available
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->$level($message, $context);
        }
    }
    
    /**
     * Create a new order from cart items with stock validation
     * 
     * @param int $user_id User ID
     * @param string $shipping_address Shipping address
     * @param bool $check_stock Whether to check stock availability
     * @param array $additional_context Additional context data for logging
     * @return array Order data with id, total, etc.
     * @throws \Exception If order creation fails
     */
    public function createOrderFromCart(
        int $user_id, 
        string $shipping_address, 
        bool $check_stock = true,
        array $additional_context = []
    ): array {
        // Validate required parameters
        if (empty($shipping_address)) {
            throw new \Exception('Shipping address is required');
        }
        
        $this->db->beginTransaction();
        
        try {
            // Get cart items query with different levels of joins based on stock check
            $cart_query = $check_stock 
                ? "SELECT c.*, p.product_name, p.price, p.discount_price, s.stock,
                   CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 
                        THEN p.discount_price ELSE p.price END as final_price 
                   FROM cart c 
                   JOIN product p ON c.product_id = p.product_id 
                   JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
                   WHERE c.user_id = ? 
                   FOR UPDATE"
                : "SELECT c.*, p.product_name, p.price, p.discount_price,
                   CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 
                        THEN p.discount_price ELSE p.price END as final_price 
                   FROM cart c 
                   JOIN product p ON c.product_id = p.product_id 
                   WHERE c.user_id = ?
                   FOR UPDATE";
            
            // Get cart items
            $cart_items = $this->db->fetchAll($cart_query, [$user_id]);
            
            if (empty($cart_items)) {
                throw new \Exception('Your cart is empty');
            }
            
            // Verify stock availability if check_stock is true
            if ($check_stock) {
                foreach ($cart_items as $item) {
                    if ($item['quantity'] > $item['stock']) {
                        throw new \Exception(
                            "Insufficient stock for {$item['product_name']} (Size: {$item['product_size']}). " .
                            "Only {$item['stock']} available."
                        );
                    }
                }
            }
            
            // Calculate order total
            $total_price = 0;
            foreach ($cart_items as $item) {
                $total_price += $item['final_price'] * $item['quantity'];
            }
            
            // Create order record
            $this->db->execute(
                "INSERT INTO orders (user_id, total_price, shipping_address, delivery_status, order_at) 
                 VALUES (?, ?, ?, 'prepare', CURRENT_TIMESTAMP)",
                [$user_id, $total_price, $shipping_address]
            );
            
            // Get the auto-generated order ID
            $order_id = $this->db->fetchOne("SELECT LAST_INSERT_ID() as id")['id'];
            
            // Create order items from cart
            foreach ($cart_items as $item) {
                $this->db->execute(
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
                
                // Update stock
                $this->db->execute(
                    "UPDATE stock SET stock = stock - ?, last_update_at = CURRENT_TIMESTAMP 
                     WHERE product_id = ? AND product_size = ?",
                    [$item['quantity'], $item['product_id'], $item['product_size']]
                );
            }
            
            // Clear the user's cart
            $this->db->execute("DELETE FROM cart WHERE user_id = ?", [$user_id]);
            
            // Log order creation
            $log_context = array_merge([
                'user_id' => $user_id,
                'order_id' => $order_id,
                'total_amount' => $total_price,
                'items_count' => count($cart_items),
            ], $additional_context);
            
            $this->log('info', 'Order created successfully', $log_context);
            
            // Commit transaction
            $this->db->commit();
            
            // Return order data
            return [
                'success' => true,
                'order_id' => $order_id,
                'total' => $total_price,
                'message' => 'Order created successfully',
                'items_count' => count($cart_items)
            ];
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            
            // Log error
            $this->log('error', 'Order creation failed: ' . $e->getMessage(), array_merge([
                'user_id' => $user_id
            ], $additional_context));
            
            // Re-throw exception
            throw $e;
        }
    }
    
    /**
     * Get order details with items and payment info
     * 
     * @param int $order_id Order ID
     * @param int|null $user_id User ID for validation (optional)
     * @return array Order data with items and payment info
     * @throws \Exception If order not found or doesn't belong to user
     */
    public function getOrderDetails(int $order_id, ?int $user_id = null): array
    {
        // Get order info with user validation if user_id provided
        $where_clause = "WHERE order_id = ?";
        $params = [$order_id];
        
        if ($user_id !== null) {
            $where_clause .= " AND user_id = ?";
            $params[] = $user_id;
        }
        
        $order = $this->db->fetchOne(
            "SELECT * FROM orders $where_clause", 
            $params
        );
        
        if (!$order) {
            $error_msg = ($user_id !== null) 
                ? "Order not found or does not belong to user" 
                : "Order not found: $order_id";
            throw new \Exception($error_msg);
        }
        
        // Get order items
        $items = $this->db->fetchAll(
            "SELECT oi.*, p.product_name, p.product_img1 
             FROM order_items oi 
             JOIN product p ON oi.product_id = p.product_id 
             WHERE oi.order_id = ?",
            [$order_id]
        );
        
        // Get payment information if exists
        $payment = $this->db->fetchOne(
            "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_at DESC LIMIT 1", 
            [$order_id]
        );
        
        // Return order details
        return [
            'order' => $order,
            'items' => $items,
            'payment' => $payment
        ];
    }
    
    /**
     * Update order status
     * 
     * @param int $order_id Order ID
     * @param string $status New status
     * @return bool Success
     * @throws \Exception If status update fails
     */
    public function updateOrderStatus(int $order_id, string $status): bool
    {
        $valid_statuses = ['prepare', 'packing', 'assign', 'shipped', 'delivered'];
        
        if (!in_array($status, $valid_statuses)) {
            throw new \Exception('Invalid status value. Valid values: ' . implode(', ', $valid_statuses));
        }
        
        $this->db->beginTransaction();
        
        try {
            // Check if order exists
            $order = $this->db->fetchOne(
                "SELECT * FROM orders WHERE order_id = ?", 
                [$order_id]
            );
            
            if (!$order) {
                throw new \Exception("Order not found: $order_id");
            }
            
            // Update order status
            $this->db->execute(
                "UPDATE orders SET delivery_status = ? WHERE order_id = ?", 
                [$status, $order_id]
            );
            
            // Commit transaction
            $this->db->commit();
            
            // Log status update
            $this->log('info', "Order status updated", [
                'order_id' => $order_id,
                'old_status' => $order['delivery_status'],
                'new_status' => $status
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->log('error', 'Order status update failed: ' . $e->getMessage(), [
                'order_id' => $order_id,
                'status' => $status
            ]);
            throw $e;
        }
    }
} 