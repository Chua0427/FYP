<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "verosports";

$connect = new mysqli($servername, $username, $password, $dbname);

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if email exists in users table (using prepared statement)
    $stmt = $connect->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry_time = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        
        // Store OTP in database (using prepared statement)
        $stmt = $connect->prepare("INSERT INTO password_resets (email, otp, ip, expiry_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $email, $otp, $ip_address, $expiry_time);
        
        if ($stmt->execute()) {
            // Send email with OTP
            $mail = new PHPMailer(true);
            
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chiannchua05@gmail.com';
                $mail->Password = 'niiwzkwxnqlecaww';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('chiannchua05@gmail.com', 'Password Reset');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';

                $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #007bff; color: white; padding: 10px; text-align: center; }
                        .content { padding: 20px; }
                        .otp { font-size: 24px; font-weight: bold; color: #007bff; }
                        .footer { margin-top: 20px; font-size: 12px; color: #777; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Password Reset Request</h2>
                        </div>
                        <div class='content'>
                            <p>You requested to reset your password. Please use the following OTP to proceed:</p>
                            <p class='otp'>$otp</p>
                            <p>This OTP is valid for 15 minutes.</p>
                            <p>If you didn't request this, please ignore this email.</p>
                        </div>
                        <div class='footer'>
                            <p>© " . date('Y') . " Your Company. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                $mail->send();
                
                // Store email in session for verification page
                $_SESSION['reset_email'] = $email;
                header("Location: verify_password_reset.php");
                exit();
                
            } catch (Exception $e) {
                echo "<script>alert('Error sending email: {$mail->ErrorInfo}'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Error processing request. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found in our system.'); window.history.back();</script>";
    }
}
?>