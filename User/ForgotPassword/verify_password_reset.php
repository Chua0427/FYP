<?php
session_start();

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

    if (empty($email)) {
        header("Location: forgot_password.php");
        exit();
    }

    if (isset($_POST['verify'])) {
        $otp = trim($_POST['otp']);
        
        // Validate OTP format
        if (!preg_match('/^\d{6}$/', $otp)) {
            $message = "Invalid OTP format. Please enter a 6-digit number.";
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $current_time = date("Y-m-d H:i:s");
            
            // Use prepared statement to prevent SQL injection
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
                
                // Redirect to password reset page
                $_SESSION['verified_for_reset'] = true;
                header("Location: reset_password.php");
                exit();
            } else {
                $message = "Invalid or expired OTP. Please try again.";
            }
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
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
            border: none;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
            position: relative;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            border: 1px solid #e0e0e0;
            transition: var(--transition);
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
            color: var(--light-text);
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            border: none;
            height: 50px;
            border-radius: var(--border-radius);
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-light), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
        }
        
        .alert-info {
            background-color: #E8F4FD;
            border-color: #B8DAF9;
            color: #0C63E4;
        }
        
        .alert-danger {
            background-color: #FCE8E6;
            border-color: #F5C2C7;
            color: var(--error-color);
        }
        
        .resend-link {
            color: var(--primary-color);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .resend-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .countdown {
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .auth-card {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 20px;
            }
            
            .auth-body {
                padding: 25px;
            }
            
            .auth-header h3 {
                font-size: 1.5rem;
            }
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
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                We've sent a 6-digit verification code to <strong><?php echo htmlspecialchars($email); ?></strong>.
                Please enter it below to continue.
            </div>
            
            <form method="POST" autocomplete="off">
                <div class="mb-4">
                    <label for="otp" class="form-label">Verification Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="text" name="otp" id="otp" class="form-control otp-input" 
                               placeholder="••••••" maxlength="6" pattern="\d{6}" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="form-text">Enter the 6-digit code sent to your email</div>
                </div>
                
                <button type="submit" name="verify" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-check-circle me-2"></i> Verify & Continue
                </button>
                
                <div class="text-center mt-3">
                    <span class="countdown" id="countdown">02:00</span> | 
                    <span class="resend-link" id="resendLink">Resend Code</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Countdown timer
        let timeLeft = 120; // 2 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const resendLink = document.getElementById('resendLink');
        
        resendLink.style.display = 'none';
        
        const timer = setInterval(() => {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownElement.style.display = 'none';
                resendLink.style.display = 'inline';
            }
        }, 1000);
        
        // Resend functionality
        resendLink.addEventListener('click', function() {
            fetch('resend_otp.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('A new verification code has been sent to your email.');
                        timeLeft = 120;
                        countdownElement.style.display = 'inline';
                        resendLink.style.display = 'none';
                        
                        const timer = setInterval(() => {
                            timeLeft--;
                            
                            const minutes = Math.floor(timeLeft / 60);
                            const seconds = timeLeft % 60;
                            
                            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                            
                            if (timeLeft <= 0) {
                                clearInterval(timer);
                                countdownElement.style.display = 'none';
                                resendLink.style.display = 'inline';
                            }
                        }, 1000);
                    } else {
                        alert('Failed to resend code. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
        
        // Auto-focus OTP input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('otp').focus();
        });
        
        // Auto move to next input (for multi-input OTP fields if you change to that)
        function moveToNext(current, nextFieldID) {
            if (current.value.length >= current.maxLength) {
                document.getElementById(nextFieldID).focus();
            }
        }
    </script>
</body>
</html>