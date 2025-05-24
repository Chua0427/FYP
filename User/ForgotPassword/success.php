<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Appointment Request</title>
  
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
  body {
    background-color: #f0f2f5;
    background-image: url('img/');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    height: 90vh;
    margin: 0;
    position: relative;
}

    .container {
      text-align: center;
      margin-left: 750px;

    }

    .success-animation {
      display: inline-block;
      position: relative;
      animation: fadeInUp 0.5s ease forwards;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .checkmark {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: block;
      stroke-width: 2;
      stroke: white;
      fill: none;
      animation: drawCircle 0.5s ease forwards;
      margin-left: 120px;
      margin-top: 100px;
    }

    .checkmark-circle {
      stroke-dasharray: 166;
      stroke-dashoffset: 166;
      stroke-width: 2;
      stroke-miterlimit: 10;
      stroke: white;
      fill: none;
      animation: strokeCircle 0.6s ease forwards;
    }

    .checkmark-check {
      transform-origin: 50% 50%;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: drawCheck 0.6s ease forwards;
    }

    .success-message {
      margin-top: 10px;
      font-family: Arial, sans-serif;
      font-size: 18px;
      color: #333;
      animation: fadeIn 0.5s ease forwards 0.6s;
    }

    @keyframes drawCircle {
      0% {
        stroke-dashoffset: 166;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes strokeCircle {
      0% {
        stroke-dashoffset: 166;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes drawCheck {
      0% {
        stroke-dashoffset: 48;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }

    /* Button styling */
    .login-link {
      display: inline-block;
      padding: 12px 24px;
      background: linear-gradient(145deg, #e0e0e0, #c0c0c0); /* Gray gradient */
      color: #333;
      text-decoration: none;
      font-size: 16px;
      font-family: Arial, sans-serif;
      border-radius: 25px; /* Rounded corners */
      border: 1px solid #b0b0b0; /* Light border */
      transition: background 0.3s ease, transform 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow */
    }

    .login-link:hover {
      background: linear-gradient(145deg, #c8c8c8, #a8a8a8); /* Darker gray gradient on hover */
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Deeper shadow on hover */
    }

    .login-link i {
      margin-left: 8px;
      transition: transform 0.3s ease;
    }

    .login-link:hover i {
      transform: translateX(5px);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="success-animation" style="margin-top: 250px;">
      <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
      </svg>
      <p class="success-message">Email Verification Process Completed</p>
      <a href="login.php" class="login-link">Click Here to login 
        <i class="fas fa-arrow-right"></i> <!-- Right arrow icon -->
      </a>
    </div>
  </div>
</body>
</html>
