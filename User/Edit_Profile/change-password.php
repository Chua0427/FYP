<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - VeroSports</title>
    <link rel="stylesheet" href="change-password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="../HomePage/images/VeroSports.jpeg" class="logo">
        <div class="nav-links">
            <a href="edit-profile.php">Edit Profile</a>
            <a href="change-password.php">Change Password</a>
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
<<<<<<< HEAD
                <button type="submit" class="btn"><b>Change Password</b></button>
                <a href="../HomePage/homePage.php" class="btn btn-cancel"><b>Cancel</b></a>
=======
                <button type="submit" class="btn">Change Password</button>
                <a href="../HomePage/homePage.php" class="btn btn-cancel">Cancel</a>
>>>>>>> a95b9eca4863135b0f7a7228aaf3e3a942d6a4d0
            </div>
        </form>
    </div>
    <script src="change-password.js"></script>
</body>
</html>