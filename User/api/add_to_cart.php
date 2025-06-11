<?php
declare(strict_types=1);

// Clean any output buffer to prevent JSON corruption
ob_clean();

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../payment/db.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/api_auth.php';
require_once __DIR__ . '/../app/csrf.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize request ID for logging
$request_id = uniqid('cart_', true);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Check if admin in view-only mode
if (isset($_SESSION['admin_view_only']) && $_SESSION['admin_view_only'] === true) {
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'error' => 'Admin in view-only mode cannot add items to cart',
        'admin_view_only' => true
    ]);
    exit;
}

// Verify user authentication and CSRF token
try {
    $user = requireApiAuth(); // This will exit with 401 if not authenticated
    $user_id = (int)$user['user_id'];

    // CSRF protection
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token. Please refresh the page and try again.');
    }

    // Initialize logger
    $logger = $GLOBALS['logger'];
    
    // Initialize Database
    $db = new Database();
    
    // Use READ COMMITTED instead of SERIALIZABLE for better performance
    // Still prevents dirty reads but allows more concurrency
    $db->execute("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
    $db->beginTransaction();
    
    // Check if this is a batch request
    $is_batch = isset($_POST['items']) && is_array($_POST['items']);
    
    if ($is_batch) {
        // Process batch of items
        $items = $_POST['items'];
        $results = [];
        $total_added = 0;
        
        foreach ($items as $item) {
            try {
                $product_id = isset($item['product_id']) ? (int)$item['product_id'] : 0;
                $product_size = isset($item['product_size']) ? htmlspecialchars(trim($item['product_size'])) : '';
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                
                $result = processCartItem($db, $user_id, $product_id, $product_size, $quantity, $logger, $request_id);
                $results[] = $result;
                
                if ($result['success']) {
                    $total_added += 1;
                }
            } catch (Exception $e) {
                $results[] = [
                    'success' => false,
                    'product_id' => $product_id ?? 0,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Commit transaction only if all operations were successful
        if (count($results) === $total_added) {
            $db->commit();
            
            // Get updated cart count
            $cart_count = $db->fetchOne(
                "SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?", 
                [$user_id]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Items added to your cart',
                'cart_count' => $cart_count ? (int)$cart_count['count'] : 0,
                'results' => $results
            ]);
            exit;
        } else {
            // Some items failed, rollback and return partial results
            $db->rollback();
            
            echo json_encode([
                'success' => false,
                'message' => 'Some items could not be added to your cart',
                'results' => $results
            ]);
            exit;
        }
    } else {
        // Process single item (traditional flow)
        // Validate required parameters with proper sanitization
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $product_size = isset($_POST['product_size']) ? htmlspecialchars(trim($_POST['product_size'])) : '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // Log the request only in development mode
        if (!$GLOBALS['isProduction']) {
            $logger->info('Add to cart request', [
                'request_id' => $request_id,
                'user_id' => $user_id,
                'product_id' => $product_id,
                'product_size' => $product_size,
                'quantity' => $quantity
            ]);
        }
        
        // Validate input
        if ($product_id <= 0) {
            throw new Exception('Invalid product ID');
        }
        
        if (empty($product_size)) {
            throw new Exception('Product size is required');
        }
        
        if ($quantity <= 0) {
            throw new Exception('Quantity must be at least 1');
        }
        
        // Check if product exists - use indexing hint for better performance
        $product = $db->fetchOne(
            "SELECT * FROM product USE INDEX (PRIMARY) WHERE product_id = ?", 
            [$product_id]
        );
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Check if stock is available - use row-level locking instead of table lock
        $stock = $db->fetchOne(
            "SELECT * FROM stock WHERE product_id = ? AND product_size = ? FOR UPDATE", 
            [$product_id, $product_size]
        );
        
        if (!$stock) {
            throw new Exception("Size {$product_size} is not available for this product");
        }
        
        if ($stock['stock'] < $quantity) {
            throw new Exception("Insufficient stock available. Only {$stock['stock']} items in stock.");
        }
        
        // Check if item already exists in cart - use indexing hint for better performance
        $cart_item = $db->fetchOne(
            "SELECT * FROM cart USE INDEX (user_product_size) WHERE user_id = ? AND product_id = ? AND product_size = ? FOR UPDATE", 
            [$user_id, $product_id, $product_size]
        );
        
        if ($cart_item) {
            // Update quantity of existing item
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            // Check if new quantity is within stock limits
            if ($new_quantity > $stock['stock']) {
                throw new Exception("Cannot add more items. Limited to {$stock['stock']} items.");
            }
            
            $db->execute(
                "UPDATE cart SET quantity = ?, added_at = CURRENT_TIMESTAMP WHERE cart_id = ?", 
                [$new_quantity, $cart_item['cart_id']]
            );
            
            $message = 'Item quantity updated in your cart';
            
            // Only log in development mode
            if (!$GLOBALS['isProduction']) {
                $logger->info('Cart item updated', [
                    'request_id' => $request_id,
                    'user_id' => $user_id,
                    'cart_id' => $cart_item['cart_id'],
                    'product_id' => $product_id,
                    'new_quantity' => $new_quantity
                ]);
            }
        } else {
            try {
                // Add new item to cart
                $db->execute(
                    "INSERT INTO cart (user_id, product_id, product_size, quantity) VALUES (?, ?, ?, ?)", 
                    [$user_id, $product_id, $product_size, $quantity]
                );
                
                $message = 'Item added to your cart';
                
                // Only log in development mode
                if (!$GLOBALS['isProduction']) {
                    $logger->info('New cart item added', [
                        'request_id' => $request_id,
                        'user_id' => $user_id,
                        'product_id' => $product_id,
                        'product_size' => $product_size,
                        'quantity' => $quantity
                    ]);
                }
            } catch (Exception $e) {
                // If duplicate entry error due to unique constraint, try again with update
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    // The item was added by another concurrent request, fetch it and update
                    $cart_item = $db->fetchOne(
                        "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND product_size = ? FOR UPDATE", 
                        [$user_id, $product_id, $product_size]
                    );
                    
                    if ($cart_item) {
                        $new_quantity = $cart_item['quantity'] + $quantity;
                        
                        // Check if new quantity is within stock limits
                        if ($new_quantity > $stock['stock']) {
                            throw new Exception("Cannot add more items. Limited to {$stock['stock']} items.");
                        }
                        
                        $db->execute(
                            "UPDATE cart SET quantity = ?, added_at = CURRENT_TIMESTAMP WHERE cart_id = ?", 
                            [$new_quantity, $cart_item['cart_id']]
                        );
                        
                        $message = 'Item quantity updated in your cart';
                    } else {
                        // This should not happen but just in case
                        throw new Exception('Failed to add item to cart: ' . $e->getMessage());
                    }
                } else {
                    // Re-throw any other exception
                    throw $e;
                }
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Get updated cart count - use cached version when possible
        $cart_count_key = "cart_count_" . $user_id;
        $cart_count = null;
        
        // Only make DB call if needed
        $cart_count = $db->fetchOne(
            "SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?", 
            [$user_id]
        );
        
        // Get product details for response
        $product_info = [
            'name' => htmlspecialchars($product['product_name']),
            'size' => htmlspecialchars($product_size),
            'price' => $product['status'] === 'Promotion' && $product['discount_price'] > 0 
                ? (float)$product['discount_price'] 
                : (float)$product['price']
        ];
        
        // Add idempotency key to response
        echo json_encode([
            'success' => true,
            'message' => $message,
            'cart_count' => $cart_count ? (int)$cart_count['count'] : 0,
            'product' => $product_info
        ]);
        exit;
    }
}
catch (Exception $e) {
    // Ensure the transaction is rolled back on any error
    if (isset($db) && $db->isTransactionActive()) {
        $db->rollback();
    }
    
    // Log the error only if it's not a common user input error
    $commonErrors = ['Invalid product ID', 'Product size is required', 'Quantity must be at least 1'];
    if (!in_array($e->getMessage(), $commonErrors)) {
        $logger->error('Add to cart error', [
            'request_id' => $request_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}

/**
 * Process a single cart item for insertion or update
 * @param Database $db Database connection
 * @param int $user_id User ID
 * @param int $product_id Product ID
 * @param string $product_size Product size
 * @param int $quantity Quantity
 * @param \Monolog\Logger $logger Logger instance
 * @param string $request_id Request ID for tracing
 * @return array Result information
 * @throws Exception If any validation fails
 */
function processCartItem(Database $db, int $user_id, int $product_id, string $product_size, int $quantity, $logger, string $request_id): array
{
    // Validate input
    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    if (empty($product_size)) {
        throw new Exception('Product size is required');
    }
    
    if ($quantity <= 0) {
        throw new Exception('Quantity must be at least 1');
    }
    
    // Check if product exists
    $product = $db->fetchOne(
        "SELECT * FROM product WHERE product_id = ?", 
        [$product_id]
    );
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Check if stock is available
    $stock = $db->fetchOne(
        "SELECT * FROM stock WHERE product_id = ? AND product_size = ? FOR UPDATE", 
        [$product_id, $product_size]
    );
    
    if (!$stock) {
        throw new Exception("Size {$product_size} is not available for this product");
    }
    
    if ($stock['stock'] < $quantity) {
        throw new Exception("Insufficient stock available. Only {$stock['stock']} items in stock.");
    }
    
    // Check if item already exists in cart
    $cart_item = $db->fetchOne(
        "SELECT * FROM cart USE INDEX (user_product_size) WHERE user_id = ? AND product_id = ? AND product_size = ? FOR UPDATE", 
        [$user_id, $product_id, $product_size]
    );
    
    if ($cart_item) {
        // Update quantity of existing item
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        // Check if new quantity is within stock limits
        if ($new_quantity > $stock['stock']) {
            throw new Exception("Cannot add more items. Limited to {$stock['stock']} items.");
        }
        
        $db->execute(
            "UPDATE cart SET quantity = ?, added_at = CURRENT_TIMESTAMP WHERE cart_id = ?", 
            [$new_quantity, $cart_item['cart_id']]
        );
        
        $logger->info('Batch cart item updated', [
            'request_id' => $request_id,
            'user_id' => $user_id,
            'cart_id' => $cart_item['cart_id'],
            'product_id' => $product_id,
            'new_quantity' => $new_quantity
        ]);
        
        return [
            'success' => true,
            'product_id' => $product_id,
            'product_size' => $product_size,
            'quantity' => $new_quantity,
            'message' => 'Item quantity updated'
        ];
    } else {
        // Add new item to cart
        $db->execute(
            "INSERT INTO cart (user_id, product_id, product_size, quantity) VALUES (?, ?, ?, ?)", 
            [$user_id, $product_id, $product_size, $quantity]
        );
        
        $logger->info('Batch new cart item added', [
            'request_id' => $request_id,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'product_size' => $product_size,
            'quantity' => $quantity
        ]);
        
        return [
            'success' => true,
            'product_id' => $product_id,
            'product_size' => $product_size,
            'quantity' => $quantity,
            'message' => 'Item added to cart'
        ];
    }
} 