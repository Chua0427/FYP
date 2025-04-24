<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
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
        .back-to-login {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Forgot Password</h3>
        <form action="send_password_reset.php" method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your registered email" required>
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary w-100">
                Send Reset Link <i class="fas fa-paper-plane"></i>
            </button>
            
            <div class="back-to-login">
                <a href="../login/login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>