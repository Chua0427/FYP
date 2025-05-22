<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | VeroSports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
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
            max-width: 450px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            border: none;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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
            margin: 0;
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
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            z-index: 4;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            border: none;
            height: 50px;
            border-radius: var(--border-radius);
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0.2), rgba(255,255,255,0));
            transform: translateX(-100%);
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-light), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
        }
        
        .btn-primary:hover::after {
            transform: translateX(100%);
        }
        
        .auth-footer {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid #f0f0f0;
            font-size: 0.95rem;
            background-color: var(--light-bg);
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }
        
        .auth-footer a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .logo {
            width: 70px;
            margin-bottom: 15px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .instruction-text {
            color: var(--light-text);
            font-size: 0.95rem;
            margin-bottom: 25px;
            text-align: center;
            line-height: 1.6;
        }
        
        .form-floating label {
            padding-left: 45px;
            color: var(--light-text);
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
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h3>Reset Your Password</h3>
        </div>
        
        <div class="auth-body">
            <p class="instruction-text">Enter your registered email address below and we'll send you a secure OTP to reset your password.</p>
            
            <form action="send_password_reset.php" method="POST">
                <div class="mb-4 position-relative">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control ps-5" placeholder="Enter your email address" required>
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-paper-plane me-2"></i> Send Verification OTP
                </button>
            </form>
        </div>
        
        <div class="auth-footer">
            <a href="../login/login.php"><i class="fas fa-arrow-left me-2"></i> Return to Login</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>