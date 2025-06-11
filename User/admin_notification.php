<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if this is actually an admin. If not, redirect to homepage
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != '2' && $_SESSION['user_type'] != '3')) {
    header('Location: /FYP/User/HomePage/homePage.php');
    exit;
}

// Get the previous page for back link
$redirect_back = '/FYP/User/HomePage/homePage.php';
$admin_dashboard = '/FYP/Admin/Dashboard/dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notification - VeroSports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .container {
            width: 550px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        
        .icon {
            font-size: 60px;
            color: #f29f05;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        p {
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            text-align: left;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #2196F3;
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #0d8bf2;
        }
        
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e8e8e8;
        }
        
        .admin-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-user-shield"></i>
        </div>
        
        <h1>Admin View-Only Mode <span class="admin-badge">ADMIN</span></h1>
        
        <div class="info-box">
            <p><strong>You are logged in as an admin.</strong> Admin accounts cannot perform user actions or access certain user functionality.</p>
        </div>
        
        <p>As an admin, you can view user pages for monitoring purposes, but cannot interact with them to maintain separation of concerns and security. Please return to the user view or admin dashboard.</p>
        
        <div class="buttons">
            <a href="<?php echo htmlspecialchars($redirect_back); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to User View
            </a>
            <a href="<?php echo htmlspecialchars($admin_dashboard); ?>" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i> Go to Admin Dashboard
            </a>
        </div>
    </div>
</body>
</html> 