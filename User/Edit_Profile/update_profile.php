<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - VeroSports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            height: 50px;
            margin-left: 20px;
        }
        
        .nav-links {
            margin-right: 20px;
        }
        
        .nav-links a {
            margin-left: 15px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        .nav-links a:hover {
            color: #4CAF50;
        }
        
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 38px;
            cursor: pointer;
            color: #777;
        }
        
        .password-toggle:hover {
            color: #333;
        }
        
        .password-strength {
            height: 5px;
            background-color: #eee;
            margin-top: 5px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            width: 100%;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .btn-container {
            margin-top: 30px;
        }
        
        .btn-cancel {
            background-color: #f44336;
            margin-top: 10px;
        }
        
        .btn-cancel:hover {
            background-color: #d32f2f;
        }
        
        .requirements {
            margin-top: 5px;
            font-size: 13px;
            color: #666;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }
        
        .requirement i {
            margin-right: 5px;
            font-size: 12px;
        }
        
        .valid {
            color: #4CAF50;
        }
        
        .invalid {
            color: #f44336;
        }
    </style>
</head>
<body>
    <header>
        <img src="../HomePage/images/VeroSports.jpeg" class="logo">
        <div class="nav-links">
            <a href="edit-profile.html">Edit Profile</a>
            <a href="change-password.html">Change Password</a>
            <a href="#">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <h1>Change Password</h1>
        
        <form id="changePasswordForm">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="current_password" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('currentPassword')"></i>
            </div>
            
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="new_password" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('newPassword')"></i>
                <div class="password-strength">
                    <div class="strength-meter" id="strengthMeter"></div>
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
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                <div id="passwordMatch" style="margin-top: 5px; font-size: 13px;"></div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn">Change Password</button>
                <a href="profile.html" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
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
        
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const strengthMeter = document.getElementById('strengthMeter');
            const requirements = {
                length: document.getElementById('lengthReq'),
                number: document.getElementById('numberReq'),
                special: document.getElementById('specialReq')
            };
            
            newPassword.addEventListener('input', function() {
                const password = newPassword.value;
                let strength = 0;
                
                // Check length
                if (password.length >= 8) {
                    requirements.length.querySelector('i').className = 'fas fa-check valid';
                    strength += 33;
                } else {
                    requirements.length.querySelector('i').className = 'fas fa-circle invalid';
                }
                
                // Check for numbers
                if (/\d/.test(password)) {
                    requirements.number.querySelector('i').className = '';
                    strength += 33;
                } else {
                    requirements.number.querySelector('i').className = 'fas fa-circle invalid';
                }
                
                // Check for special characters
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    requirements.special.querySelector('i').className = 'fas fa-check valid';
                    strength += 34;
                } else {
                    requirements.special.querySelector('i').className = 'fas fa-circle invalid';
                }
                
                // Update strength meter
                strengthMeter.style.width = strength + '%';
                if (strength < 33) {
                    strengthMeter.style.backgroundColor = '#f44336';
                } else if (strength < 66) {
                    strengthMeter.style.backgroundColor = '#ff9800';
                } else {
                    strengthMeter.style.backgroundColor = '#4CAF50';
                }
                
                // Check password match
                checkPasswordMatch();
            });
            
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            function checkPasswordMatch() {
                const matchElement = document.getElementById('passwordMatch');
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value === confirmPassword.value) {
                        matchElement.textContent = 'Passwords match';
                        matchElement.style.color = '#4CAF50';
                    } else {
                        matchElement.textContent = 'Passwords do not match';
                        matchElement.style.color = '#f44336';
                    }
                } else {
                    matchElement.textContent = '';
                }
            }
            
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (newPassword.value !== confirmPassword.value) {
                    alert('Passwords do not match!');
                    return;
                }
                
                // Here you would send the data to your backend
                // const formData = new FormData(this);
                // fetch('/change-password', { method: 'POST', body: formData })
                // .then(response => response.json())
                // .then(data => {
                //     if (data.success) {
                //         alert('Password changed successfully!');
                //         window.location.href = 'profile.html';
                //     } else {
                //         alert('Error: ' + data.message);
                //     }
                // });
                
                alert('Password changed successfully! (This is a demo)');
                window.location.href = 'profile.html';
            });
        });
    </script>
</body>
</html>