<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Enhanced Login Form</title>
    <link rel="stylesheet" type="text/css" href=".css">
</head>
<body>
    <div class="login-container">
        <h5 id="login-title" class="text-white text-center">Login Form</h5>

        <!-- Display error message if available -->
        <?php if (!empty($error_message)): ?>
            <div id="message" class="alert alert-danger show" role="alert" style="margin-top:-70px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="" method="post">
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" name="username" id="username" placeholder=" " required autocomplete="off">
                <label for="username">Username</label>
            </div>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder=" " required autocomplete="off">
                <label for="password">Password</label>
                <i class="fas fa-eye eye-icon text-right" id="togglePassword"></i>
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember" id="remember"> Remember Me
                </label>
                <a href="#" style="color:lawngreen;">Forgot Password?</a>
            </div>
            <button type="submit">Login</button>

            <br><a href="index.php" style="color:white;">You dont have account signup</a>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
