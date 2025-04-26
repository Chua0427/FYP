<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "verosports";

$connect = new mysqli($servername, $username, $password, $dbname);

$message = "";
$email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';

if (empty($email)) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['verify'])) {
    $otp = $_POST['otp'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $current_time = date("Y-m-d H:i:s");

    // Verify OTP
    $sql = "SELECT * FROM password_resets 
            WHERE email = '$email' 
            AND otp = '$otp' 
            AND ip = '$ip_address'
            AND expiry_time > '$current_time'
            AND used = 0";
    
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        // Mark OTP as used
        $sql = "UPDATE password_resets SET used = 1 WHERE email = '$email' AND otp = '$otp'";
        $connect->query($sql);
        
        // Redirect to password reset page
        $_SESSION['verified_for_reset'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "Invalid or expired OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Verify OTP</h3>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            We've sent an OTP to <strong><?php echo htmlspecialchars($email); ?></strong>. Please check your email.
        </div>
        
        <form method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" required>
            </div>
            
            <button type="submit" name="verify" class="btn btn-primary w-100">
                Verify OTP <i class="fas fa-check"></i>
            </button>
            
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Back to Forgot Password
                </a>
            </div>
        </form>
    </div>
</body>
</html>