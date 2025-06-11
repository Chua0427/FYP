<?php
declare(strict_types=1);

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once '/xampp/htdocs/FYP/User/payment/db.php';

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
     * @param array|null $selected_cart_ids Optional array of selected cart IDs (if null, all cart items are used)
     * @param bool $clear_cart Whether to clear the cart after order creation
     * @return array Order data with id, total, etc.
     * @throws \Exception If order creation fails
     */
    public function createOrderFromCart(
        int $user_id, 
        string $shipping_address, 
        bool $check_stock = true,
        array $additional_context = [],
        ?array $selected_cart_ids = null,
        bool $clear_cart = false
    ): array {
        // Validate required parameters
        if (empty($shipping_address)) {
            throw new \Exception('Shipping address is required');
        }
        
        $this->db->beginTransaction();
        
        try {
            // Build the base query
            $base_query = $check_stock 
                ? "SELECT c.*, p.product_name, p.price, p.discount_price, s.stock,
                   CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 
                        THEN p.discount_price ELSE p.price END as final_price 
                   FROM cart c 
                   JOIN product p ON c.product_id = p.product_id 
                   JOIN stock s ON c.product_id = s.product_id AND c.product_size = s.product_size
                   WHERE c.user_id = ?"
                : "SELECT c.*, p.product_name, p.price, p.discount_price,
                   CASE WHEN p.discount_price IS NOT NULL AND p.discount_price > 0 
                        THEN p.discount_price ELSE p.price END as final_price 
                   FROM cart c 
                   JOIN product p ON c.product_id = p.product_id 
                   WHERE c.user_id = ?";
            
            // Add selection filter if specific cart items are selected
            $params = [$user_id];
            if ($selected_cart_ids !== null && !empty($selected_cart_ids)) {
                $placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));
                $base_query .= " AND c.cart_id IN ($placeholders)";
                $params = array_merge($params, $selected_cart_ids);
            }
            
            // Add FOR UPDATE to lock rows
            $cart_query = $base_query . " FOR UPDATE";
            
            // Get cart items
            $cart_items = $this->db->fetchAll($cart_query, $params);
            
            if (empty($cart_items)) {
                throw new \Exception('No items selected for checkout');
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
            
            // Create order record with pending status (will update to 'prepare' after payment success)
            $this->db->execute(
                "INSERT INTO orders (user_id, total_price, shipping_address, delivery_status, order_at) 
                 VALUES (?, ?, ?, 'pending', CURRENT_TIMESTAMP)",
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
                
                // Stock will be updated only after payment is confirmed, not at order creation
                // Removed the stock update code from here
            }
            
            // Clear the user's cart if requested
            if ($clear_cart) {
                if ($selected_cart_ids !== null && !empty($selected_cart_ids)) {
                    // Delete only selected cart items
                    $placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));
                    $this->db->execute(
                        "DELETE FROM cart WHERE user_id = ? AND cart_id IN ($placeholders)", 
                        array_merge([$user_id], $selected_cart_ids)
                    );
                } else {
                    // Delete all cart items
                    $this->db->execute("DELETE FROM cart WHERE user_id = ?", [$user_id]);
                }
            }
            
            // Store the order ID in the session for cart retention logic
            $_SESSION['current_order_id'] = $order_id;
            
            // Log order creation
            $log_context = array_merge([
                'user_id' => $user_id,
                'order_id' => $order_id,
                'total_amount' => $total_price,
                'items_count' => count($cart_items),
                'selected_items' => $selected_cart_ids !== null ? count($selected_cart_ids) : 'all'
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
    
    /**
     * Update stock levels after payment confirmation
     * 
     * @param int $order_id Order ID
     * @return bool True if stock update was successful
     * @throws \Exception If stock update fails
     */
    public function updateStockAfterPayment(int $order_id): bool
    {
        $this->db->beginTransaction();
        
        try {
            // Get order items
            $orderItems = $this->db->fetchAll(
                "SELECT oi.* FROM order_items oi WHERE oi.order_id = ?",
                [$order_id]
            );
            
            if (empty($orderItems)) {
                throw new \Exception('No items found for this order');
            }
            
            $this->log('info', 'Starting stock update for order', ['order_id' => $order_id, 'items_count' => count($orderItems)]);
            
            // Update stock for each item
            foreach ($orderItems as $item) {
                // Check current stock
                $stockInfo = $this->db->fetchOne(
                    "SELECT stock_id, stock FROM stock WHERE product_id = ? AND product_size = ? FOR UPDATE",
                    [$item['product_id'], $item['product_size']]
                );
                
                if (!$stockInfo) {
                    $this->log('error', 'Stock record not found', [
                        'order_id' => $order_id,
                        'product_id' => $item['product_id'],
                        'size' => $item['product_size']
                    ]);
                    throw new \Exception("Stock record not found for product ID {$item['product_id']}, size {$item['product_size']}");
                }
                
                if ($stockInfo['stock'] < $item['quantity']) {
                    $this->log('error', 'Insufficient stock', [
                        'order_id' => $order_id,
                        'product_id' => $item['product_id'],
                        'size' => $item['product_size'],
                        'requested' => $item['quantity'],
                        'available' => $stockInfo['stock']
                    ]);
                    throw new \Exception(
                        "Insufficient stock for product ID {$item['product_id']}, size {$item['product_size']}. " .
                        "Required: {$item['quantity']}, Available: {$stockInfo['stock']}"
                    );
                }
                
                // Calculate new stock level
                $newStock = $stockInfo['stock'] - $item['quantity'];
                
                // Update stock
                $result = $this->db->execute(
                    "UPDATE stock SET stock = ?, last_update_at = CURRENT_TIMESTAMP 
                     WHERE product_id = ? AND product_size = ?",
                    [$newStock, $item['product_id'], $item['product_size']]
                );
                
                // Log the stock update details
                $this->log('info', 'Stock updated for item', [
                    'order_id' => $order_id,
                    'product_id' => $item['product_id'],
                    'size' => $item['product_size'],
                    'stock_id' => $stockInfo['stock_id'] ?? 'unknown',
                    'previous_stock' => $stockInfo['stock'],
                    'quantity_reduced' => $item['quantity'],
                    'new_stock' => $newStock,
                    'rows_affected' => $result
                ]);
            }
            
            // Log stock update
            $this->log('info', 'Stock update completed successfully for order', [
                'order_id' => $order_id,
                'items_count' => count($orderItems)
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            
            $this->log('error', 'Failed to update stock after payment: ' . $e->getMessage(), [
                'order_id' => $order_id
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Verify Stripe payment status directly from Stripe API
     * 
     * @param string $stripe_id Stripe payment intent or charge ID
     * @param string $payment_id Local payment ID for logging
     * @param string|null $apiKey Optional Stripe API key to use directly
     * @return bool True if payment is confirmed as successful
     * @throws \Exception If verification fails or payment is not successful
     */
    public function verifyStripePayment(string $stripe_id, string $payment_id, ?string $apiKey = null): bool
    {
        try {
            // If API key is provided directly, use it
            if ($apiKey) {
                \Stripe\Stripe::setApiKey($apiKey);
            } else {
                // Load Stripe API key from secrets file
                require_once '/xampp/htdocs/FYP/User/payment/secrets.php';
                
                // Initialize Stripe SDK
                \Stripe\Stripe::setApiKey($stripeSecretKey);
            }
            
            // Check if ID starts with 'pi_' (payment intent) or 'ch_' (charge)
            if (strpos($stripe_id, 'pi_') === 0) {
                $payment = \Stripe\PaymentIntent::retrieve($stripe_id);
                $is_successful = $payment->status === 'succeeded';
            } elseif (strpos($stripe_id, 'ch_') === 0) {
                $payment = \Stripe\Charge::retrieve($stripe_id);
                $is_successful = $payment->status === 'succeeded' && $payment->paid === true;
            } elseif (strpos($stripe_id, 'cs_') === 0) {
                // Checkout Session
                $session = \Stripe\Checkout\Session::retrieve($stripe_id);
                $is_successful = $session->payment_status === 'paid';
            } else {
                throw new \Exception("Unrecognized Stripe ID format: $stripe_id");
            }
            
            // Log verification result
            $this->log('info', 'Stripe payment verification', [
                'payment_id' => $payment_id,
                'stripe_id' => $stripe_id,
                'is_successful' => $is_successful,
                'status' => $payment->status ?? $session->payment_status ?? 'unknown'
            ]);
            
            // Update payment log
            $this->db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'info', ?)",
                [$payment_id, "Stripe verification: " . ($is_successful ? 'Confirmed successful' : 'Not successful')]
            );
            
            return $is_successful;
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log Stripe API error
            $this->log('error', 'Stripe verification error', [
                'payment_id' => $payment_id,
                'stripe_id' => $stripe_id,
                'error' => $e->getMessage()
            ]);
            
            // Update payment log
            $this->db->execute(
                "INSERT INTO payment_log (payment_id, log_level, log_message) 
                 VALUES (?, 'error', ?)",
                [$payment_id, "Stripe verification error: " . $e->getMessage()]
            );
            
            throw new \Exception("Failed to verify payment with Stripe: " . $e->getMessage());
        }
    }
} 