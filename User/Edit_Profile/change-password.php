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
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
    $error_message = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error_message = "Password must contain at least one number";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
        $error_message = "Password must contain at least one special character";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['password_change_success'] = true;
            header("Location: change-password.php");
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
                    <div class="requirement" id="uppercaseReq"> <!-- 新增 -->
                        <i class="fas fa-circle"></i>
                        <span>Contains an uppercase letter</span>
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
    
    <div id="successPopup" class="popup">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <i class="fas fa-check-circle success-icon"></i>
            <h2>Success!</h2>
            <p>Your password has been changed successfully.</p>
            <a href="../HomePage/homePage.php" class="popup-ok-btn">OK</a>

        </div>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['password_change_success']) && $_SESSION['password_change_success']): ?>
        showSuccessPopup();
        <?php unset($_SESSION['password_change_success']); ?>
    <?php endif; ?>
});

function showSuccessPopup() {
    const popup = document.getElementById('successPopup');
    popup.style.display = 'flex';
    
    // Close popup when clicking X or OK button
    document.querySelector('.close-popup').addEventListener('click', function() {
        popup.style.display = 'none';
    });
    
    document.querySelector('.popup-ok-btn').addEventListener('click', function() {
        popup.style.display = 'none';
    });
    
    // Close when clicking outside the popup
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });
}
</script>
    <script src="change-password.js"></script>
</body>
</html>