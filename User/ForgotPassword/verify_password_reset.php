<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "verosports";

try {
    $connect = new mysqli($servername, $username, $password, $dbname);
    
    if ($connect->connect_error) {
        throw new Exception("Connection failed: " . $connect->connect_error);
    }

    $message = "";
    $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';

    // Redirect if no email in session
    if (empty($email)) {
        header("Location: forgot_password.php");
        exit();
    }

    // Handle OTP verification
    if (isset($_POST['verify'])) {
        $otp = trim($_POST['otp']);
        
        // Validate OTP format
        if (!preg_match('/^\d{6}$/', $otp)) {
            $message = "Invalid OTP format. Please enter a 6-digit number.";
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $current_time = date("Y-m-d H:i:s");
            
            // Verify OTP with prepared statement
            $stmt = $connect->prepare("SELECT * FROM password_resets 
                                     WHERE email = ? 
                                     AND otp = ? 
                                     AND ip = ?
                                     AND expiry_time > ?
                                     AND used = 0");
            $stmt->bind_param("ssss", $email, $otp, $ip_address, $current_time);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Mark OTP as used
                $update_stmt = $connect->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND otp = ?");
                $update_stmt->bind_param("ss", $email, $otp);
                $update_stmt->execute();
                
                // Set verification flag
                $_SESSION['verified_for_reset'] = true;
                header("Location: reset_password.php");
                exit();
            } else {
                $message = "Invalid or expired OTP. Please try again.";
            }
        }
    }

    // Handle OTP resend request
    if (isset($_POST['resend'])) {
        // Delete existing unused OTPs
        $delete_stmt = $connect->prepare("DELETE FROM password_resets WHERE email = ? AND used = 0");
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();

        // Generate new OTP
        $new_otp = rand(100000, 999999);
        $expiry_time = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Insert new OTP record
        $insert_stmt = $connect->prepare("INSERT INTO password_resets (email, otp, ip, expiry_time) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("siss", $email, $new_otp, $ip_address, $expiry_time);
        
        if ($insert_stmt->execute()) {
            // Send email with new OTP
            require 'phpmailer/src/Exception.php';
            require 'phpmailer/src/PHPMailer.php';
            require 'phpmailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chiannchua05@gmail.com';
                $mail->Password = 'niiwzkwxnqlecaww';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('chiannchua05@gmail.com', 'Password Reset');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'New Password Reset OTP';

                $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #007bff; color: white; padding: 10px; text-align: center; }
                        .content { padding: 20px; }
                        .otp { font-size: 24px; font-weight: bold; color: #007bff; }
                        .footer { margin-top: 20px; font-size: 12px; color: #777; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>New Verification Code</h2>
                        </div>
                        <div class='content'>
                            <p>Your new verification code is:</p>
                            <p class='otp'>$new_otp</p>
                            <p>This code is valid for 15 minutes.</p>
                            <p>If you didn't request this, please ignore this email.</p>
                        </div>
                        <div class='footer'>
                            <p>© " . date('Y') . " VeroSports. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                $mail->send();
                $message = "New verification code has been sent to your email!";
            } catch (Exception $e) {
                $message = "Error sending email: " . $e->getMessage();
            }
        } else {
            $message = "Error generating new verification code.";
        }
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $message = "A system error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | VeroSports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-light: #FF8C42;
            --secondary-color: #2B2D42;
            --light-bg: #F8F9FA;
            --dark-text: #2B2D42;
            --light-text: #8D99AE;
            --error-color: #E63946;
            --success-color: #4BB543;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #FFE8D9 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: var(--dark-text);
            padding: 20px;
        }
        
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .otp-input {
            letter-spacing: 2px;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }
        
        .form-control {
            height: 50px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            border: none;
            height: 50px;
            border-radius: var(--border-radius);
            font-weight: 600;
        }
        
        .alert {
            border-radius: var(--border-radius);
        }
        
        .resend-link {
            color: var(--primary-color);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .countdown {
            color: var(--light-text);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h3>Verify Your Identity</h3>
        </div>
        
        <div class="auth-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= strpos($message, 'Error') !== false ? 'danger' : 'success' ?> mb-4">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Verification code sent to <strong><?= htmlspecialchars($email) ?></strong>
            </div>
            
            <form method="POST" autocomplete="off">
                <div class="mb-4">
                    <label class="form-label">Enter 6-digit Code</label>
                    <input type="text" name="otp" class="form-control otp-input" 
                           placeholder="••••••" maxlength="6" required
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                
                <button type="submit" name="verify" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-check-circle me-2"></i> Verify
                </button>
                
                <div class="text-center mt-3">
                    <form method="POST">
                        <span class="countdown" id="countdown">02:00</span> | 
                        <button type="submit" name="resend" class="resend-link" id="resendLink">
                            Resend Code
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer
        let timeLeft = 120;
        const countdownElement = document.getElementById('countdown');
        const resendLink = document.getElementById('resendLink');
        
        resendLink.style.display = 'none';
        
        const timer = setInterval(() => {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownElement.style.display = 'none';
                resendLink.style.display = 'inline';
            }
        }, 1000);
    </script>
</body>
</html>