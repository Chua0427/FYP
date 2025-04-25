<?php
declare(strict_types=1);
session_start();
require_once '/xampp/htdocs/FYP/vendor/autoload.php';
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/../app/auth.php';

// PHP Mailer for OTP resending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Check if user is already authenticated with token
if (Auth::check()) {
    header('Location: /FYP/FYP/User/HomePage/homePage.php');
    exit;
}

// Check if we have temp user data from login process
if (!isset($_SESSION['temp_user']) || !isset($_SESSION['login_otp'])) {
    // No temp user data, redirect back to login
    header('Location: login.php');
    exit;
}

// Check OTP expiry
if (isset($_SESSION['otp_expiry']) && time() > $_SESSION['otp_expiry']) {
    // OTP expired, clear temp data and redirect to login with message
    unset($_SESSION['login_otp']);
    unset($_SESSION['temp_user']);
    unset($_SESSION['temp_remember']);
    unset($_SESSION['otp_expiry']);
    
    $_SESSION['login_error'] = 'OTP has expired. Please try again.';
    header('Location: login.php');
    exit;
}

// Check if we need to send OTP email (after redirect from login.php)
if (isset($_SESSION['send_otp']) && $_SESSION['send_otp'] === true) {
    // Clear the flag to prevent multiple sends
    unset($_SESSION['send_otp']);
    
    // Get user data from session
    $user = $_SESSION['temp_user'];
    $otp = $_SESSION['login_otp'];
    
    // Set OTP expiry if not already set
    if (!isset($_SESSION['otp_expiry'])) {
        $_SESSION['otp_expiry'] = time() + 600; // 10 minutes expiry
    }
    
    // Log start of email sending
    error_log("Starting OTP email send after redirect at " . date('H:i:s'));
    $start_time = microtime(true);
    
    // Send OTP via email
    $otp_sent = sendOTPEmail($user['email'], $otp, $user['first_name']);
    
    // Log completion of email sending
    $send_time = microtime(true) - $start_time;
    error_log("Completed OTP email send after redirect at " . date('H:i:s') . 
              " - time: " . number_format($send_time, 4) . "s - result: " . 
              ($otp_sent ? 'success' : 'failed'));
    
    if (!$otp_sent) {
        $error = 'Failed to send OTP to your email. You can try resending it.';
        error_log("OTP email send failed after redirect: " . $error);
    } else {
        // Log successful OTP send
        $GLOBALS['authLogger']->info('OTP sent after redirect', [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'send_time' => number_format($send_time, 4)
        ]);
    }
}

// Get redirect URL from session
$redirect = $_SESSION['redirect_after_login'] ?? '/FYP/FYP/User/HomePage/homePage.php';
$error = '';
$success_message = '';

// Function to generate a random OTP
function generateOTP() {
    return mt_rand(100000, 999999); // 6-digit OTP
}

// Function to send OTP via email
function sendOTPEmail($email, $otp, $user_name) {
    // Full paths to PHPMailer files using __DIR__ for reliability
    require_once __DIR__ . '/../../otp/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../../otp/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../otp/phpmailer/src/SMTP.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'DeadHunter0802@gmail.com';
        $mail->Password = 'drzrsnnjezzdrfvx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Set timeout values to prevent long waits
        $mail->Timeout = 10; // Timeout for SMTP connection (in seconds)
        $mail->SMTPKeepAlive = false; // Don't keep connection alive for multiple emails
        
        // Recipients
        $mail->setFrom('DeadHunter0802@gmail.com', 'VeroSports Authentication');
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
    } catch (\Exception $e) {
        error_log("General Error in OTP Email: " . $e->getMessage());
        return false;
    }
}

// Handle OTP verification
if (isset($_POST['otp_submit'])) {
    $entered_otp = $_POST['otp_code'] ?? '';
    $stored_otp = $_SESSION['login_otp'] ?? '';
    
    // Validate OTP
    if (!empty($entered_otp) && !empty($stored_otp)) {
        // Check if OTP has expired
        if (isOTPExpired()) {
            $error = "OTP has expired. Please request a new one.";
        } else if ($entered_otp == $stored_otp) {
            // OTP is valid
            $GLOBALS['authLogger']->info('User successfully verified OTP and logged in', [
                'email' => $_SESSION['temp_user']['email'],
                'user_id' => $_SESSION['temp_user']['user_id']
            ]);
            
            // Copy from temp_user to user session
            $_SESSION['user_id'] = $_SESSION['temp_user']['user_id'];
            $_SESSION['first_name'] = $_SESSION['temp_user']['first_name'];
            $_SESSION['last_name'] = $_SESSION['temp_user']['last_name'];
            $_SESSION['email'] = $_SESSION['temp_user']['email'];
            $_SESSION['user_type'] = $_SESSION['temp_user']['user_type'];
            
            // Set authentication fingerprint to prevent session hijacking
            $_SESSION['auth_fingerprint'] = hash('sha256', 
                $_SERVER['HTTP_USER_AGENT'] . 
                ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . 
                $_SESSION['user_id']
            );
            
            // Create persistent token if remember me was checked
            $remember_me = isset($_SESSION['remember_me']) && $_SESSION['remember_me'] === true;
            if ($remember_me) {
                Auth::login($_SESSION['user_id'], $_SESSION['temp_user'], true);
            }
            
            // Clear sensitive session data
            unset($_SESSION['login_otp']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['temp_user']);
            unset($_SESSION['remember_me']);
            
            // Redirect to homepage or the intended destination
            $redirect = $_SESSION['redirect_after_login'] ?? '/FYP/FYP/User/HomePage/homePage.php';
            unset($_SESSION['redirect_after_login']);
            
            header("Location: " . $redirect);
            exit();
        } else {
            $error = "Invalid OTP. Please try again.";
        }
    } else {
        $error = "Please enter the OTP sent to your email.";
    }
}

// Handle OTP resend
if (isset($_POST['resend_otp'])) {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }
        
        // Verify we still have the user data
        if (!isset($_SESSION['temp_user']) || !is_array($_SESSION['temp_user'])) {
            throw new Exception('Session expired. Please login again.');
        }
        
        // Reset OTP expiry time
        $_SESSION['otp_expiry'] = time() + 600; // 10 minutes expiry
        
        // Generate new OTP
        $otp = generateOTP();
        $_SESSION['login_otp'] = $otp;
        
        $user = $_SESSION['temp_user'];
        
        // Send OTP
        $otp_sent = sendOTPEmail($user['email'], $otp, $user['first_name']);
        
        if (!$otp_sent) {
            throw new Exception('Failed to send OTP to your email');
        }
        
        // Log OTP resend
        $GLOBALS['authLogger']->info('OTP resent', [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        
        $success = 'New OTP has been sent to your email';
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log('OTP resend failed: ' . $error);
    }
}

function isOTPExpired() {
    // Check if OTP is expired based on timestamp
    if (!isset($_SESSION['otp_expiry'])) {
        return true;
    }
    
    return time() > $_SESSION['otp_expiry'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - VeroSports</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .otp-input {
            letter-spacing: 10px;
            font-size: 20px;
            text-align: center;
            font-weight: bold;
        }
        .resend-otp {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .timer {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .email-info {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }
        .email-highlight {
            font-weight: bold;
            color: #333;
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .link-button {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
            padding: 0;
        }
        .link-button:hover {
            color: #0056b3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <h1>VeroSports</h1>
            <p>The Real Sports Equipment<br>Unleash Your True Potential</p>
        </div>
        <div class="right-section">
            <h2>Verification Required</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="email-info">
                We've sent a verification code to<br>
                <span class="email-highlight"><?php echo htmlspecialchars($_SESSION['temp_user']['email']); ?></span>
            </div>
            
            <!-- OTP Verification Form -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="text" class="input-box otp-input" name="otp_code" placeholder="Enter OTP" maxlength="6" required autofocus>
                <div class="timer" id="otpTimer">
                    OTP expires in: <span id="countdown">10:00</span>
                </div>
                <button type="submit" name="otp_submit" class="login-btn">Verify &amp; Login</button>
            </form>
            
            <!-- Resend OTP Form -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="resend-otp">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                Didn't receive the code? 
                <button type="submit" name="resend_otp" id="resendBtn" class="link-button">Resend OTP</button>
                <span id="resendCountdown" style="display: none;">(Wait: <span id="resendTimer">60</span>s)</span>
            </form>
            
            <div class="back-to-login">
                <form action="login.php" method="get">
                    <button type="submit" class="link-button">Back to Login</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // OTP expiry countdown
        const endTime = new Date();
        <?php if (isset($_SESSION['otp_expiry'])): ?>
        endTime.setTime(<?php echo $_SESSION['otp_expiry'] ?> * 1000);
        <?php else: ?>
        endTime.setTime(endTime.getTime() + 10 * 60 * 1000); // 10 minutes from now
        <?php endif; ?>
        
        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;
            
            if (timeLeft <= 0) {
                document.getElementById('countdown').textContent = "Expired";
                // Redirect to login page after short delay
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 3000);
                return;
            }
            
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').textContent = 
                minutes.toString().padStart(2, '0') + ':' + 
                seconds.toString().padStart(2, '0');
                
            setTimeout(updateCountdown, 1000);
        }
        
        updateCountdown();
        
        // Resend cooldown timer
        const resendBtn = document.getElementById('resendBtn');
        const resendCountdown = document.getElementById('resendCountdown');
        const resendTimer = document.getElementById('resendTimer');
        let resendCooldown = 60; // 60 seconds cooldown
        
        <?php if (!empty($success_message)): ?>
        startResendCooldown();
        <?php endif; ?>
        
        function startResendCooldown() {
            resendBtn.disabled = true;
            resendCountdown.style.display = 'inline';
            resendTimer.textContent = resendCooldown;
            
            const countdownInterval = setInterval(() => {
                resendCooldown--;
                resendTimer.textContent = resendCooldown;
                
                if (resendCooldown <= 0) {
                    clearInterval(countdownInterval);
                    resendBtn.disabled = false;
                    resendCountdown.style.display = 'none';
                    resendCooldown = 60;
                }
            }, 1000);
        }
    });
    </script>
</body>
</html> 