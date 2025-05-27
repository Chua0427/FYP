<?php
declare(strict_types=1);

// Restrict admin access to user pages
require_once __DIR__ . '/../app/restrict_admin.php';

// Load auth check and notification system
require_once __DIR__ . '/../app/auth-check.php';

// Initialize session if not already started
if (isset($GLOBALS['session_started']) || session_status() === PHP_SESSION_ACTIVE) {
    // Session already started in init.php or elsewhere
} else if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions
require_once __DIR__ . '/../app/auth.php';

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ensure the Auth class is properly initialized
Auth::init();

// Check authentication - this will check both token and session auth
$is_authenticated = Auth::check();
$user_data = null;

if ($is_authenticated) {
    $user_data = Auth::user();
    
    // Update session for backward compatibility if not already set or if values don't match
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user_data['user_id']) {
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['first_name'] = $user_data['first_name'] ?? '';
        $_SESSION['last_name'] = $user_data['last_name'] ?? '';
        $_SESSION['email'] = $user_data['email'] ?? '';
        $_SESSION['user_type'] = $user_data['user_type'] ?? 0;
        
        // Set session fingerprint
        $_SESSION['auth_fingerprint'] = hash('sha256', 
            $_SERVER['HTTP_USER_AGENT'] . 
            ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
            $user_data['user_id']
        );
    }
}

// Get cart count if user is authenticated
$cartCount = 0;
if ($is_authenticated) {
    require_once '/xampp/htdocs/FYP/FYP/User/payment/db.php';
    
    try {
        $db = new Database();
        $result = $db->fetchOne(
            "SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?",
            [$_SESSION['user_id']]
        );
        
        if ($result) {
            $cartCount = (int)$result['count'];
        }
        
        $db->close();
    } catch (Exception $e) {
        // Silently fail
        error_log("Cart count error: " . $e->getMessage());
    }
}

// Add auth notification resources
add_auth_notification_resources();
?>

<header>
    <img src="../Header_and_Footer/img/VeroSports.jpeg" class="logo">

    <div class="subtitleContainer">
        <div class="subTitle">
            <ul>
                <li id="home">
                    <a href="../HomePage/homePage.php">Home</a>
                </li>
                <li id="newArrival">
                    <a href="../New_Arrival_Page/new_product.php">New Arrivals</a>
                </li>
                <div class="dropdown">
                    <li id="men"><a href="../All_Product_Page/all_product.php?gender=Men">Men</a></li>
                    <div class="dropdownmenu">
                        <div class="category">
                            <h3>Footwear</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Boot">Boot</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Futsal">Futsal</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Running">Running</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Court">Court</a>
                        </div>
                        <div class="category">
                            <h3>Apparel</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Jersey">Jerseys</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Jacket">Jackets</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Pant">Pants</a>
                        </div>
                        <div class="category">
                            <h3>Equipment</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Bag">Bags</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Cap">Caps</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Football Accessories">Football Accessories</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Sock">Socks</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&product_categories=Gym Accessories">Gym Accessories</a>
                        </div>
                        <div class="category">
                            <h3>Shop By Brand</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Nike">Nike</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Adidas">Adidas</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Puma">Puma</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Umbro">Umbro</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Lotto">Lotto</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Asics">Asics</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=New Balance">New Balance</a>
                            <a href="../All_Product_Page/all_product.php?gender=Men&brand=Under Amour">Under Amour</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <li id="women"><a href="../All_Product_Page/all_product.php?gender=Women">Women</a></li>
                    <div class="dropdownmenu">
                        <div class="category">
                            <h3>Footwear</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Training">Training</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Running">Running</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Court">Court</a>
                        </div>
                        <div class="category">
                            <h3>Apparel</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Jersey">Jerseys</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Jacket">Jackets</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Legging">Leggings</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Pant">Pants</a>
                        </div>
                        <div class="category">
                            <h3>Equipment</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Bag">Bags</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Cap">Caps</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Sock">Socks</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&product_categories=Gym Accessories">Gym Accessories</a>
                        </div>
                        <div class="category">
                            <h3>Shop By Brand</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Nike">Nike</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Adidas">Adidas</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Puma">Puma</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Lotto">Lotto</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Asics">Asics</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=New Balance">New Balance</a>
                            <a href="../All_Product_Page/all_product.php?gender=Women&brand=Umbro">Umbro</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <li id="kids"><a href="../All_Product_Page/all_product.php?gender=Kid">Kids</a></li>
                    <div class="dropdownmenu">
                        <div class="category">
                            <h3>Footwear</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Football Shoes">Football Shoes</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Kid Shoes">Kids Shoes</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=School Shoes">School Shoes</a>
                        </div>
                        <div class="category">
                            <h3>Apparel</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Jacket">Jackets</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Jersey">Jerseys</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Pant">Pants</a>
                        </div>
                        <div class="category">
                            <h3>Equipment</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Bag">Bag</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Cap">Caps</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Football Accessories">Football Accessories</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Sock">Socks</a>
                        </div>
                        <div class="category">
                            <h3>Shop By Brand</h3>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&brand=Nike">Nike</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&brand=Adidas">Adidas</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&brand=Puma">Puma</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&brand=Umbro">Umbro</a>
                            <a href="../All_Product_Page/all_product.php?gender=Kid&brand=Under Amour">Under Amour</a>
                        </div>
                    </div>
                </div>
                <li id="promotion">
                    <a href="../Promotion_Page/promotion_product.php">Promotion</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="iconContainer">
        <div class="icon">
            <a href="../Search/search.php">
            <div class="search">
                <i class="fa-solid fa-search"></i>
            </div>
        </a>
            <div class="user">
                <?php if ($is_authenticated): ?>
                    <div class="user-dropdown">
                        <i class="fa-solid fa-user"></i>
                        <div class="user-dropdown-content">
                            <div class="user-info">
                                <span>Hello, <?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?></span>
                            </div>

                            <a href="../order/orderhistory.php">My Order History</a>
                            <a href="../View_Order/order.php">My Orders</a>

                            <a href="../Edit_Profile/profile.php">My Profile</a>
                            <a href="../login/manage_sessions.php">Manage Devices</a>

                            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 2): ?>
                                <a href="/FYP/FYP/Admin/Dashboard/dashboard.php">Admin Dashboard</a>
                            <?php endif; ?>

                            <a href="../login/logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../login/login.php"><i class="fa-solid fa-user"></i></a>
                <?php endif; ?>
            </div>
            <div class="shoppingCart">
                <a href="../order/cart.php" <?php if (!$is_authenticated) echo requires_auth_attr(false); ?>>
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($is_authenticated && $cartCount > 0): ?>
                        <span id="cartCount" class="cart-counter"><?php echo $cartCount; ?></span>
                    <?php else: ?>
                        <span id="cartCount" class="cart-counter" style="display:none;">0</span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</header>

<style>
/* Add these styles for the user dropdown */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    right: 0;
    border-radius: 4px;
}

.user-dropdown:hover .user-dropdown-content {
    display: block;
}

.user-dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    font-size: 14px;
}

.user-info {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
    font-weight: bold;
    font-size: 14px;
}

.user-dropdown-content a:hover {
    background-color: #f8f8f8;
}

/* Improved cart counter that's always a perfect circle */
.cart-counter {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: red;
    color: white;
    /* Fixed dimensions to ensure perfect circle */
    width: 20px;
    height: 20px;
    /* Use flexbox for perfect centering */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
    /* Prevent text wrapping or overflow */
    text-align: center;
    overflow: hidden;
}

.shoppingCart {
    position: relative;
}
</style>

<!-- Search popup and related JavaScript has been removed, but the search icon in the header is maintained -->

<script>
    // Adjust cart count font size based on the number of digits
    document.addEventListener('DOMContentLoaded', function() {
        // Update keyframes for the pulse animation to prevent vertical shift
        const styleSheet = document.createElement('style');
        styleSheet.id = 'cart-counter-animations';
        styleSheet.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(styleSheet);
        
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
        
        // Run initially
        adjustCartCountSize();
        
        // Set up a MutationObserver to watch for changes to the cart count
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            const observer = new MutationObserver(adjustCartCountSize);
            observer.observe(cartCount, { childList: true, subtree: true, characterData: true });
            
            // Override the pulse animation to prevent vertical shifting
            cartCount.addEventListener('animationstart', function() {
                cartCount.style.transformOrigin = 'center center';
            });
        }
    });
</script>

<!-- Search popup and related JavaScript has been removed, but the search icon in the header is maintained --> 