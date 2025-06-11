<?php
declare(strict_types=1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../payment/db.php';
require __DIR__ . '/../app/init.php';

// Initialize session if not already started
ensure_session_started();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Initialize Database
    $db = new Database();
    
    // Remove cart entries for products marked as deleted
    $db->execute(
        "DELETE c FROM cart c JOIN product p ON c.product_id = p.product_id WHERE p.deleted = 1 AND c.user_id = ?",
        [$user_id]
    );
    
    // Fetch total cart count
    $result = $db->fetchOne(
        "SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?",
        [$user_id]
    );
    
    $cartCount = (int)($result['count'] ?? 0);
    
    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount
    ]);
    
} catch (Exception $e) {
    // Log the error
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->error('Failed to fetch cart count', [
            'user_id' => $user_id,
            'error' => $e->getMessage()
        ]);
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to fetch cart count: ' . $e->getMessage()
    ]);
} 