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
    
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "verosports";
        
        $connect = new mysqli($servername, $username, $password, $dbname);
        
        $sql = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
        
        if ($connect->query($sql)) {
            // Clear session and redirect to login
            session_unset();
            session_destroy();
            header("Location: ../login/login.php?reset=success");
            exit();
        } else {
            $message = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .form-container {
            max-width: 450px;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #343a40;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .form-control {
            padding-left: 45px;
        }
        .input-group-text {
            width: 40px;
            justify-content: center;
            background-color: #f8f9fa;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .password-strength {
            margin-top: 5px;
            font-size: 14px;
        }
        .strength-weak { color: red; }
        .strength-medium { color: orange; }
        .strength-strong { color: green; }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Reset Password</h3>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" disabled>
            </div>
            
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
                <span class="input-group-text eye-icon" onclick="togglePassword('new_password')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div class="password-strength" id="password-strength"></div>
            
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                <span class="input-group-text eye-icon" onclick="togglePassword('confirm_password')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            
            <button type="submit" name="reset" class="btn btn-primary w-100">
                Reset Password <i class="fas fa-sync-alt"></i>
            </button>
        </form>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('i');
            
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
        
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('password-strength');
            let strength = 0;
            
            if (password.length === 0) {
                strengthText.textContent = '';
                return;
            }
            
            // Check for length
            if (password.length >= 8) strength++;
            
            // Check for uppercase letters
            if (/[A-Z]/.test(password)) strength++;
            
            // Check for numbers
            if (/[0-9]/.test(password)) strength++;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update strength text
            switch(strength) {
                case 0:
                case 1:
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'password-strength strength-weak';
                    break;
                case 2:
                case 3:
                    strengthText.textContent = 'Medium';
                    strengthText.className = 'password-strength strength-medium';
                    break;
                case 4:
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'password-strength strength-strong';
                    break;
            }
        });
    </script>
</body>
</html>