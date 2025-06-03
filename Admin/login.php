<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/brute_force_protection.php';

// Generate or validate CSRF token with expiration
function generateCSRFToken(): void {
    // Create a new token that expires in 30 minutes
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time() + 1800; // 30 minutes expiration
}

function validateCSRFToken(?string $token): bool {
    // Token doesn't exist or doesn't match
    if (empty($_SESSION['csrf_token']) || empty($token) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    // Token is expired
    if (time() > ($_SESSION['csrf_token_time'] ?? 0)) {
        // Generate a new token for the form and fail validation
        generateCSRFToken();
        return false;
    }
    
    return true;
}

// Generate CSRF token if not exists or expired
if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || time() > $_SESSION['csrf_token_time']) {
    generateCSRFToken();
}

$error = '';
$is_locked = false;

// Check for error message from redirect
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Check for login_error in session
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

// Initialize brute force protection
try {
    $bruteForceProtection = new BruteForceProtection();
} catch (Exception $e) {
    error_log("Failed to initialize brute force protection: " . $e->getMessage());
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        // Validate CSRF token
        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            throw new Exception('Invalid CSRF token');
        }

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

        // Check if the account is locked out
        if (isset($bruteForceProtection)) {
            $lockout_info = $bruteForceProtection->isLockedOut($email);
            if ($lockout_info['is_locked']) {
                throw new Exception($lockout_info['message']);
            }
        }

        // Database connection
        $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // User not found - record failed attempt but don't reveal this information
        if (!$user) {
            if (isset($bruteForceProtection)) {
                $bruteForceProtection->recordFailedAttempt($email);
            }
            throw new Exception('Invalid email or password');
        }

        // Check if the user is an admin (user_type = 2 or user_type = 3)
        if (!isset($user['user_type']) || ($user['user_type'] != 2 && $user['user_type'] != 3)) {
            if (isset($bruteForceProtection)) {
                $bruteForceProtection->recordFailedAttempt($email);
            }
            throw new Exception('This login page is for admin accounts only');
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Authentication successful
            
            // Record successful login and clear lockouts
            if (isset($bruteForceProtection)) {
                $bruteForceProtection->recordSuccessfulLogin($email);
                $bruteForceProtection->clearLockouts($email);
            }
            
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
            
            // Set secure admin authentication cookie
            setcookie('admin_logged_in', 'true', [
                'expires' => time() + 3600, // 1 hour
                'path' => '/FYP/FYP/Admin/',
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            // Log successful admin login
            if (isset($GLOBALS['authLogger'])) {
                $GLOBALS['authLogger']->info('Admin login successful', [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
            
            // Redirect to admin dashboard
            header("Location: /FYP/FYP/Admin/Dashboard/dashboard.php");
            exit;
        } else {
            // Failed password - record failed attempt
            if (isset($bruteForceProtection)) {
                $bruteForceProtection->recordFailedAttempt($email);
                
                // Check if this attempt triggered a lockout
                $lockout_info = $bruteForceProtection->isLockedOut($email);
                if ($lockout_info['is_locked']) {
                    throw new Exception($lockout_info['message']);
                }
            }
            
            throw new Exception('Invalid email or password');
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("Admin login database error: " . $e->getMessage());
    } catch (Exception $e) {
        $error = $e->getMessage();
        
        // Check if this is a lockout message
        if (strpos($error, 'temporarily locked') !== false) {
            $is_locked = true;
        }
    }
    
    // Regenerate CSRF token after each login attempt
    generateCSRFToken();
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
        
        .login-btn[disabled] {
            background-color: #999;
            cursor: not-allowed;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .lockout-info {
            margin-top: 10px;
            padding: 8px;
            background-color: #ffecec;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .error-message i {
            margin-right: 5px;
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
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <?php if ($is_locked): ?>
                        <div class="lockout-info">
                            <i class="fas fa-lock"></i> For security reasons, please wait before trying again.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="login-form" <?php echo $is_locked ? 'disabled' : ''; ?>>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="email" class="input-box" name="email" placeholder="Email" required>
                <input type="password" class="input-box" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="login-btn" <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <?php if ($is_locked): ?>
                        <i class="fas fa-lock"></i> Account Locked
                    <?php else: ?>
                        <i class="fas fa-sign-in-alt"></i> Login
                    <?php endif; ?>
                </button>
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