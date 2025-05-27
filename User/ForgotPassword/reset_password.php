<?php
session_start();

if (!isset($_SESSION['verified_for_reset']) || !$_SESSION['verified_for_reset']) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$message = "";

if (isset($_POST['reset'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // 新增密码复杂度验证
    $passwordError = [];
    
    if ($new_password !== $confirm_password) {
        $passwordError[] = "Passwords do not match";
    }
    if (strlen($new_password) < 8) {
        $passwordError[] = "Password must be at least 8 characters";
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $passwordError[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[0-9]/', $new_password)) {
        $passwordError[] = "Password must contain at least one number";
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
        $passwordError[] = "Password must contain at least one special character";
    }

    if (!empty($passwordError)) {
        $errorList = implode("<br>", $passwordError);
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>'.$errorList.'
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // 使用预处理语句防止SQL注入
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "verosports";
        
        try {
            $connect = new mysqli($servername, $username, $password, $dbname);
            
            $sql = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if ($stmt->execute()) {
                session_unset();
                session_destroy();
                header("Location: ../login/login.php?reset=success");
                exit();
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> Error updating password
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Database error
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | VeroSports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-light: #FF8C42;
            --secondary-color: #2B2D42;
            --accent-color: #4CC9F0;
            --light-bg: #F8F9FA;
            --dark-text: #2B2D42;
            --light-text: #8D99AE;
            --success-color: #4BB543;
            --warning-color: #FFA500;
            --danger-color: #DC3545;
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
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .auth-header h3 {
            margin: 15px 0 0;
            font-weight: 600;
            font-size: 1.8rem;
            position: relative;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .auth-body {
            padding: 35px;
        }
        
        .form-control {
            height: 50px;
            border-radius: var(--border-radius);
            border: 1px solid #e0e0e0;
            padding-left: 50px;
            transition: var(--transition);
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 27%;
            transform: translateY(-50%);
            color: var(--light-text);
            z-index: 4;
            font-size: 1.1rem;
        }
        
        .password-toggle {
    position: absolute;
    right: 18px;
    top: 27%; 
    transform: translateY(-50%); 
    color: var(--light-text);
    cursor: pointer;
    z-index: 4;
    transition: var(--transition);
}

.password-toggl { 
    position: absolute;
    right: 18px;
    top: 65%;
    transform: translateY(-50%); 
    color: var(--light-text);
    cursor: pointer;
    z-index: 4;
    transition: var(--transition);
}
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            border: none;
            height: 50px;
            border-radius: var(--border-radius);
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-light), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
        }
        
        .password-strength-meter {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            width: 0%;
            transition: var(--transition);
        }
        
        .strength-weak {
            background-color: var(--danger-color);
            width: 33%;
        }
        
        .strength-medium {
            background-color: var(--warning-color);
            width: 66%;
        }
        
        .strength-strong {
            background-color: var(--success-color);
            width: 100%;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: var(--light-text);
            margin-top: 15px;
        }
        
        .requirement {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .requirement i {
            margin-right: 8px;
            font-size: 0.7rem;
            transition: var(--transition);
        }
        
        .requirement.valid {
            color: var(--success-color);
        }
        
        .requirement.valid i {
            color: var(--success-color);
        }
        
        #password-match {
            font-size: 0.85rem;
            margin-top: 8px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: white;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .alert {
            border-radius: var(--border-radius);
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .auth-card {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 25px;
            }
            
            .auth-body {
                padding: 25px;
            }
            
            .auth-header h3 {
                font-size: 1.5rem;
            }
            
            .logo {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h3>Create New Password</h3>
        </div>
        
        <div class="auth-body">
            <?php echo $message; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" disabled>
                </div>
                
                <div class="mb-4 position-relative">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control ps-5" placeholder="Enter new password" required>
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
                    <div class="password-strength-meter">
                        <div class="password-strength-fill" id="password-strength-fill"></div>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-length">
                            <i class="fas fa-circle"></i> At least 8 characters
                        </div>
                        <div class="requirement" id="req-uppercase">
                            <i class="fas fa-circle"></i> At least 1 uppercase letter
                        </div>
                        <div class="requirement" id="req-number">
                            <i class="fas fa-circle"></i> At least 1 number
                        </div>
                        <div class="requirement" id="req-special">
                            <i class="fas fa-circle"></i> At least 1 special character
                        </div>
                    </div>
                </div>
                
                <div class="mb-5 position-relative">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control ps-5" placeholder="Confirm new password" required>
                    <i class="fas fa-eye password-toggl" onclick="togglePassword('confirm_password', this)"></i>
                    <div id="password-match" class="mt-2"></div>
                </div>
                
                <button type="submit" name="reset" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt me-2"></i> Reset Password
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength and validation
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('password-strength-fill');
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');
            
            // Reset classes
            strengthFill.className = 'password-strength-fill';
            [reqLength, reqUppercase, reqNumber, reqSpecial].forEach(el => {
                el.classList.remove('valid');
                el.querySelector('i').className = 'fas fa-circle';
            });
            
            if (password.length === 0) {
                strengthFill.style.width = '0%';
                return;
            }
            
            let strength = 0;
            
            // Check length
            if (password.length >= 8) {
                strength++;
                reqLength.classList.add('valid');
                reqLength.querySelector('i').className = 'fas fa-check';
            }
            
            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength++;
                reqUppercase.classList.add('valid');
                reqUppercase.querySelector('i').className = 'fas fa-check';
            }
            
            // Check number
            if (/[0-9]/.test(password)) {
                strength++;
                reqNumber.classList.add('valid');
                reqNumber.querySelector('i').className = 'fas fa-check';
            }
            
            // Check special character
            if (/[^A-Za-z0-9]/.test(password)) {
                strength++;
                reqSpecial.classList.add('valid');
                reqSpecial.querySelector('i').className = 'fas fa-check';
            }
            
            // Update strength meter
            switch(strength) {
                case 1:
                    strengthFill.className = 'password-strength-fill strength-weak';
                    break;
                case 2:
                case 3:
                    strengthFill.className = 'password-strength-fill strength-medium';
                    break;
                case 4:
                    strengthFill.className = 'password-strength-fill strength-strong';
                    break;
                default:
                    strengthFill.className = 'password-strength-fill';
            }
        });
        
        // Password match verification
        document.getElementById('confirm_password').addEventListener('input', function() {
            const confirmPassword = this.value;
            const newPassword = document.getElementById('new_password').value;
            const matchText = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                return;
            }
            
            if (confirmPassword === newPassword) {
                matchText.innerHTML = '<i class="fas fa-check-circle me-2"></i> Passwords match!';
                matchText.style.color = 'var(--success-color)';
            } else {
                matchText.innerHTML = '<i class="fas fa-times-circle me-2"></i> Passwords do not match';
                matchText.style.color = 'var(--danger-color)';
            }
        });
        document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('new_password').value;
    const errors = [];
    
    if (password.length < 8) errors.push("Minimum 8 characters");
    if (!/[A-Z]/.test(password)) errors.push("At least one uppercase letter");
    if (!/[0-9]/.test(password)) errors.push("At least one number");
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) errors.push("At least one special character");
    
    if (errors.length > 0) {
        e.preventDefault();
        alert("Password requirements not met:\n" + errors.join('\n'));
    }
});
    </script>
</body>
</html>