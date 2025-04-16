<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - VeroSports</title>
    <link rel="stylesheet" href="Profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="../HomePage/images/VeroSports.jpeg" class="logo">
        <nav class="main-nav">
            <a href="#">New Arrivals</a>
            <a href="#">Men</a>
            <a href="#">Women</a>
            <a href="#">Kids</a>
            <a href="#">Sports</a>
            <a href="#">Brands</a>
            <a href="#">Sale</a>
        </nav>
        <div class="header-actions">
            <div class="search-box">
                <input type="text" placeholder="Search product or brand">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </header>

    <div class="profile-container">
        <aside class="profile-sidebar">
            <h3>My Account</h3>
            <ul>
                <li><a href="profile.php"><i class="fas fa-user"></i> My details</a></li>
                <li><a href="#"><i class="fas fa-shopping-bag"></i> My orders</a></li>
                <li class="active"><a href="change_password.php"><i class="fas fa-lock"></i> Change password</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i> Manage address book</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Contact preferences</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign out</a></li>
            </ul>
        </aside>

        <main class="profile-content">
            <h1>Change Password</h1>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form class="profile-form" method="POST" action="update_password.php">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                    <div class="password-strength">
                        <span class="strength-bar"></span>
                        <span class="strength-text"></span>
                    </div>
                    <ul class="password-requirements">
                        <li data-requirement="length">At least 8 characters</li>
                        <li data-requirement="uppercase">At least 1 uppercase letter</li>
                        <li data-requirement="number">At least 1 number</li>
                        <li data-requirement="special">At least 1 special character</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                    <span id="password-match" class="validation-message"></span>
                </div>

                <button type="submit" class="save-btn">Update Password</button>
            </form>
        </main>
    </div>

    <script src="js/password.js"></script>
</body>
</html>