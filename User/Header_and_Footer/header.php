<?php
declare(strict_types=1);

// Restrict admin access to user pages
require_once __DIR__ . '/../app/restrict_admin.php';

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
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
            "SELECT COUNT(*) as count FROM cart WHERE user_id = ?",
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
                            <a href="../All_Product_Page/all_product.php?gender=Kid&product_categories=Paint">Paints</a>
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
                            <a href="../View_Order/view_order.php">My Orders</a>

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
                <?php if ($is_authenticated): ?>
                <a href="../order/cart.php">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($cartCount > 0): ?>
                        <span id="cartCount" class="cart-counter"><?php echo $cartCount; ?></span>
                    <?php else: ?>
                        <span id="cartCount" class="cart-counter" style="display:none;">0</span>
                    <?php endif; ?>
                </a>
                <?php else: ?>
                <a href="../login/login.php?redirect=<?php echo urlencode('/FYP/FYP/User/order/cart.php'); ?>">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
                <?php endif; ?>
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

.cart-counter {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    font-weight: bold;
}

.shoppingCart {
    position: relative;
}
</style>

<!-- Search popup and related JavaScript has been removed, but the search icon in the header is maintained --> 