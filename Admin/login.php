<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

// Check for error message from redirect
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Check for empty fields
        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Fetch user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception('Invalid email or password');
        }

        // Check if the user is an admin (user_type = 2 or 3)
        if (!isset($user['user_type']) || ($user['user_type'] != 2 && $user['user_type'] != 3)) {
            throw new Exception('This login page is for admin accounts only');
        }

        if (password_verify($password, $user['password'])) {
            // Authentication successful
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Set authentication fingerprint
            $_SESSION['auth_fingerprint'] = hash('sha256', 
                $_SERVER['HTTP_USER_AGENT'] . 
                ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
                $user['user_id']
            );
            
            // Redirect to admin dashboard
            header("Location: /FYP/FYP/Admin/Dashboard/dashboard.php");
            exit;
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - VeroSports</title>
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
        }
        
        .container {
            display: flex;
            width: 800px;
            height: 500px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .left-section {
            flex: 0.4;
            background: #333;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .left-section h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .left-section p {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.8;
        }
        
        .right-section {
            flex: 0.6;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        
        .right-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        
        .input-box {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background-color: #555;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .admin-note {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #333;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <h1>VeroSports Admin</h1>
            <p>Admin Control Panel<br>Manage Products, Orders, and Users</p>
        </div>
        <div class="right-section">
            <h2>Admin Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="email" class="input-box" name="email" placeholder="Email" required>
                <input type="password" class="input-box" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="login-btn">Login</button>
            </form>
            
            <div class="admin-note">
                <p>This login page is for admin accounts only.</p>
                <p>Regular users please use the customer login page.</p>
            </div>
            
            <div class="back-link">
                <a href="/FYP/FYP/User/HomePage/homePage.php"><i class="fas fa-arrow-left"></i> Back to Main Site</a>
            </div>
        </div>
    </div>
</body>
</html> 