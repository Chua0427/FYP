<?php
declare(strict_types=1);

// Only start session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/app/auth.php';

// Check if user is admin (types 2 or 3)
if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 2 || $_SESSION['user_type'] == 3 || $_SESSION['user_type'] == '2' || $_SESSION['user_type'] == '3')) {
    // Set the admin view-only mode flag
    $_SESSION['admin_view_only'] = true;
    
    // Log the admin accessing user pages
    if (isset($GLOBALS['authLogger'])) {
        $GLOBALS['authLogger']->info('Admin accessed user area in view-only mode', [
            'admin_id' => $_SESSION['user_id'],
            'user_type' => $_SESSION['user_type'],
            'requested_url' => $_SERVER['REQUEST_URI'],
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    // Allow search operations in view-only mode without redirect
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    if ($currentPage === 'search_product.php') {
        return;
    }
    
    // Restricted folders for admin view-only mode
    $restrictedFolders = [
        '/order/',
        '/login/',
        '/Registration/',
        '/Review_Page/',
        '/View_Order/',
        '/Delivery_Status_Page/',
        '/Edit_Profile/'
    ];
    
    // Check if current URI contains any restricted folder
    $currentUri = $_SERVER['REQUEST_URI'];
    $inRestrictedFolder = false;
    foreach ($restrictedFolders as $folder) {
        if (strpos($currentUri, $folder) !== false) {
            $inRestrictedFolder = true;
            break;
        }
    }

    // If this is a request that would change data, requires a redirect, contact-us form, 
    // or is in a restricted folder, show notification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
        isset($_GET['action']) || 
        strpos($currentUri, 'checkout') !== false ||
        strpos($currentUri, 'payment') !== false ||
        strpos($currentUri, 'profile') !== false ||
        strpos($currentUri, 'contact') !== false ||
        $inRestrictedFolder) {
        
        // Store the current URL for the redirect back
        $_SESSION['admin_redirect_from'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to admin notification page
        header('Location: /FYP/User/admin_notification.php');
        exit;
    }
}

// Regular user - continue normally
?> 