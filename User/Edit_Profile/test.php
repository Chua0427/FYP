<?php
session_start();
require_once 'db-connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Fetch current password hash
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $error_message = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match";
    } elseif (strlen($new_password) < 8) {
        $error_message = "Password must be at least 8 characters long";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Password changed successfully!";
            header("Location: profile.php");
            exit();
        } else {
            $error_message = "Error changing password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - VeroSports</title>
    <link rel="stylesheet" href="change-password.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Add styles for requirement icons */
        .requirements {
            margin-top: 10px;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 14px;
            color: #666;
        }
        
        .requirement i {
            margin-right: 8px;
            font-size: 12px;
        }
        
        .requirement .fa-circle {
            color: #ccc;
        }
        
        .requirement .fa-check {
            color: #4CAF50;
        }
        
        .validation-message {
            margin-top: 5px;
            font-size: 13px;
        }
        
        .validation-message.valid {
            color: #4CAF50;
        }
        
        .validation-message.invalid {
            color: #f44336;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="change-password-container">
        <h1>Change Password</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form id="passwordForm" action="change-password.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <div class="password-input">
                    <input type="password" id="current_password" name="current_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password', this)"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="password-input">
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password', this)"></i>
                </div>
                <div class="password-strength">
                    <span id="strengthText">Password Strength: </span>
                    <span id="strengthMeter">
                        <span class="strength-bar weak"></span>
                        <span class="strength-bar medium"></span>
                        <span class="strength-bar strong"></span>
                    </span>
                </div>
                <div class="requirements">
                    <div class="requirement" id="lengthReq">
                        <i class="fas fa-circle"></i>
                        <span>At least 8 characters</span>
                    </div>
                    <div class="requirement" id="numberReq">
                        <i class="fas fa-circle"></i>
                        <span>Contains a number</span>
                    </div>
                    <div class="requirement" id="specialReq">
                        <i class="fas fa-circle"></i>
                        <span>Contains a special character</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="password-input">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                </div>
                <span id="passwordMatch" class="validation-message"></span>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Change Password</button>
                <a href="profile.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

    <script>
        // Toggle password visibility
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker with requirement icons
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('strengthText');
            const bars = document.querySelectorAll('.strength-bar');
            
            // Reset all bars
            bars.forEach(bar => {
                bar.style.width = '0%';
                bar.className = 'strength-bar';
            });
            
            // Get requirement icons
            const lengthIcon = document.querySelector('#lengthReq i');
            const numberIcon = document.querySelector('#numberReq i');
            const specialIcon = document.querySelector('#specialReq i');
            
            if (password.length === 0) {
                strengthText.textContent = 'Password Strength: ';
                // Reset all icons to circle
                lengthIcon.className = 'fas fa-circle';
                numberIcon.className = 'fas fa-circle';
                specialIcon.className = 'fas fa-circle';
                return;
            }
            
            // Check requirements and update icons
            // Length requirement
            if (password.length >= 8) {
                lengthIcon.className = 'fas fa-check';
            } else {
                lengthIcon.className = 'fas fa-circle';
            }
            
            // Number requirement
            if (/\d/.test(password)) {
                numberIcon.className = 'fas fa-check';
            } else {
                numberIcon.className = 'fas fa-circle';
            }
            
            // Special character requirement
            if (/[^A-Za-z0-9]/.test(password)) {
                specialIcon.className = 'fas fa-check';
            } else {
                specialIcon.className = 'fas fa-circle';
            }
            
            // Calculate strength
            let strength = 0;
            if (password.length >= 8) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update UI
            let strengthLabel = '';
            let strengthClass = '';
            
            if (strength <= 1) {
                strengthLabel = 'Weak';
                strengthClass = 'weak';
                bars[0].style.width = '33%';
            } else if (strength <= 2) {
                strengthLabel = 'Medium';
                strengthClass = 'medium';
                bars[0].style.width = '33%';
                bars[1].style.width = '33%';
            } else {
                strengthLabel = 'Strong';
                strengthClass = 'strong';
                bars[0].style.width = '33%';
                bars[1].style.width = '33%';
                bars[2].style.width = '33%';
            }
            
            strengthText.textContent = `Password Strength: ${strengthLabel}`;
            bars.forEach(bar => {
                if (bar.style.width !== '0%') {
                    bar.classList.add(strengthClass);
                }
            });
        });

        // Confirm password match
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                matchText.className = 'validation-message';
            } else if (newPassword === confirmPassword) {
                matchText.textContent = 'Passwords match';
                matchText.className = 'validation-message valid';
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.className = 'validation-message invalid';
            }
        });

        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                e.preventDefault();
                return;
            }
            
            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>