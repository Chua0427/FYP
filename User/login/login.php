<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// Check if user is already authenticated with token
if (Auth::check()) {
    header('Location: /FYP/FYP/User/HomePage/homePage.php');
    exit;
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if there's a redirect URL
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/FYP/FYP/User/HomePage/homePage.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        $pdo = new PDO('mysql:host=localhost;dbname=verosports', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

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

        if ($user && password_verify($password, $user['password'])) {
            // Create a token only if remember me is checked
            if ($remember) {
                // Password is correct, generate and store token for long-term use
                $token = Auth::login($user['user_id'], $user, true);
                
                // Log remember me usage
                $GLOBALS['authLogger']->info('User enabled Remember Me', [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            } else {
                // No persistent token, just use the session
                Auth::loginWithoutToken($user);
                
                $GLOBALS['authLogger']->info('User login without Remember Me', [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            }
            
            // For backward compatibility, also set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Log successful login using global logger
            $GLOBALS['authLogger']->info('User login', [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            
            // Regenerate session ID after login for security
            session_regenerate_id(true);
            
            // For enhanced security, store a fingerprint of this session 
            // This helps prevent session hijacking when using session-only auth
            $_SESSION['auth_fingerprint'] = hash('sha256', 
                $_SERVER['HTTP_USER_AGENT'] . 
                ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
                $user['user_id']
            );
            
            // Redirect to the requested page or homepage
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = "Invalid email or password.";
            
            // Log failed login attempt using global logger
            $GLOBALS['authLogger']->warning('Failed login attempt', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        $GLOBALS['logger']->error('Login PDO error', [
            'message' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    } catch (Exception $e) {
        $error = $e->getMessage();
        $GLOBALS['logger']->error('Login error', [
            'message' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VeroSports</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="left-section">
            <h1>VeroSports</h1>
            <p>The Real Sports Equipment<br>Unleash Your True Potential</p>
        </div>
        <div class="right-section">
            <h2>Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="email" class="input-box" name="email" placeholder="Email" required>
                <input type="password" class="input-box" name="password" placeholder="Password" required>
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <button type="submit" class="login-btn">Login</button>
                <a href="../ForgotPassword/forgot_password.php" class="forgot-password">Forgot Password?</a>
            </form>
            
            <div class="divider"><span>OR</span></div>
            
            <div class="social-buttons">
                <button type="button">
                    <i class="fab fa-facebook-f" style="color:#3b5998; margin-right:8px;"></i> Facebook
                </button>
                <button type="button">
                    <i class="fab fa-google" style="color:#db4437; margin-right:8px;"></i> Google
                </button>
            </div>
            
            <div class="signup-text">
                Don't have an account? <a href="../Registration/Register.php">Sign Up</a>
            </div>
        </div>
    </div>
</body>
</html>