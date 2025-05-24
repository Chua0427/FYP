<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// PHP Mailer for OTP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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

// Function to generate a random OTP
function generateOTP() {
    return mt_rand(100000, 999999); // 6-digit OTP
}

// Function to send OTP via email
function sendOTPEmail($email, $otp, $user_name) {
    require_once __DIR__ . '/../otp/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../otp/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../otp/phpmailer/src/SMTP.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'chiannchua05@gmail.com';
        $mail->Password = 'niiwzkwxnqlecaww';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Set timeout values to prevent long waits
        $mail->Timeout = 10; // Timeout for SMTP connection (in seconds)
        $mail->SMTPKeepAlive = false; // Don't keep connection alive for multiple emails
        
        // Recipients
        $mail->setFrom('chiannchua05@gmail.com', 'VeroSports Authentication');
        $mail->addAddress($email, $user_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your VeroSports Login OTP';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;'>
                <h2 style='color: #333; text-align: center;'>VeroSports Authentication</h2>
                <p>Hello {$user_name},</p>
                <p>Your One-Time Password (OTP) for login is:</p>
                <div style='background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; border-radius: 5px;'>
                    {$otp}
                </div>
                <p style='color: #777; font-size: 14px; margin-top: 20px;'>This OTP is valid for 10 minutes. Please do not share it with anyone.</p>
                <p style='color: #777; font-size: 14px;'>If you did not request this OTP, please ignore this email.</p>
            </div>
        ";
        
        // Set priority to speed up delivery
        $mail->Priority = 1; // High priority
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle username/password authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        // Start timing for performance tracking
        $start_time = microtime(true);
        
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

        // Log DB query time
        $db_time = microtime(true) - $start_time;
        error_log("Database query time: " . number_format($db_time, 4) . " seconds");

        if (password_verify($password, $user['password'])) {
            // Authentication successful
            $auth_time = microtime(true) - $start_time;
            error_log("Password verification successful at " . date('H:i:s') . " - Time: " . number_format($auth_time, 4) . "s");
            
            // Check if user is an admin (user_type = 2)
            if (isset($user['user_type']) && $user['user_type'] == 2) {
                $error = 'Admin accounts must use the admin login page.';
                $GLOBALS['authLogger']->warning('Admin attempted to login on user page', [
                    'email' => $email,
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
                
                // Stop processing
                throw new Exception('Admin accounts must use the admin login page.');
            }
            
            // Generate OTP
            $otp = generateOTP();
            
            // Store OTP and user data in session for verification
            $_SESSION['login_otp'] = $otp;
            $_SESSION['temp_user'] = $user;
            $_SESSION['login_time'] = time();
            $_SESSION['remember_me'] = $remember;
            $_SESSION['redirect_after_login'] = $redirect;
            
            // Set flag to send OTP email after redirect
            $_SESSION['send_otp'] = true;
            
            // Log the OTP generation
            $GLOBALS['authLogger']->info('OTP generated for login', [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'ip' => $_SERVER['REMOTE_ADDR'],
                'auth_time' => number_format($auth_time, 4)
            ]);
            
            // Record total processing time before redirect
            $total_time = microtime(true) - $start_time;
            error_log("Total login processing time before redirect: " . number_format($total_time, 4) . "s");
            
            // Redirect to OTP verification page
            header("Location: verify_login_otp.php");
            exit;
        } else {
            $error = 'Invalid email or password.';
            error_log("Login failed: Invalid password for email {$email}");
            $GLOBALS['authLogger']->warning('Failed login attempt', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'reason' => 'invalid_password'
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
            
            <!-- Login Form (Email/Password) -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="email" class="input-box" name="email" placeholder="Email" required>
                <input type="password" class="input-box" name="password" placeholder="Password" required>
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <button type="submit" name="login" class="login-btn">Login</button>
                <a href="../ForgotPassword/forgot_password.php" class="forgot-password">Forgot Password?</a>
            </form>
            
            <div class="signup-text">
                Don't have an account? <a href="../Registration/Register.php">Sign Up</a>
            </div>
        </div>
    </div>
</body>
</html>