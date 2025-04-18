<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sent_otp";

$connect = new mysqli($servername,$username,$password,$dbname);

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['send'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $otp = $_POST['otp'];

    $ip_address = $_SERVER['REMOTE_ADDR'];

    $sql = "INSERT INTO otp (name,email,phone,password,otp,status,otp_send_time,ip) VALUES ('$name','$email','$phone','$password','$otp','pending',NOW(),'$ip_address')";

    if ($connect->query($sql) === TRUE) {

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'DeadHunter0802@gmail.com';

            $mail->Password = 'drzrsnnjezzdrfvx';
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('DeadHunter0802@gmail.com', 'Change Password OTP Verification');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $_POST['subject'];

            // Constructing a well-structured, professional email body with CSS
            $mail->Body = "
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f7fc;
                        color: #333;
                        padding: 20px;
                    }
                    .email-container {
                        background-color: #ffffff;
                        border-radius: 8px;
                        padding: 30px;
                        border: 1px solid #ddd;
                        max-width: 600px;
                        margin: 0 auto;
                    }
                    .header {
                        background-color: #007bff;
                        color: white;
                        padding: 20px;
                        text-align: center;
                        border-radius: 8px 8px 0 0;
                    }
                    .content {
                        margin-top: 20px;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 30px;
                        font-size: 12px;
                        color: #aaa;
                    }
                    .button {
                        background-color: #007bff;
                        color: white;
                        padding: 12px 30px;
                        border-radius: 5px;
                        text-decoration: none;
                    }
                    .button:hover {
                        background-color: #0056b3;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='header'>
                        <h2>OTP Verification Code</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>$name</strong>,</p>
                        <p>Thank you for registering with us! To complete your registration, please use the following One-Time Password (OTP) to verify your email address:</p>
                        <h3 style='color: #007bff;'>Your OTP Code: $otp</h3>
                        <p>This OTP is valid for the next 15 minutes. Please do not share it with anyone.</p>
                        <p>If you didn't request this, please ignore this email.</p>
                        <p>Best regards,<br><strong>Vector Coding Team</strong></p>
                        <a href='http://localhost/otp/verify.php' class='button'style='background-color:black;color:white;'>Verify Now</a>
                    </div>
                    <div class='footer'>
                        <p>For support, contact us at <a href='mailto:verosports.com'>verosportsgmail.com</a></p>
                    </div>
                </div>
            </body>
            </html>
            ";

            // Send the email
            $mail->send();
            echo "
            <script>
                alert('Verification code has been sent to your email.');
                document.location.href='verify.php';
            </script>";

        } catch (Exception $e) {
            echo "
            <script>
                alert('Error: {$mail->ErrorInfo}');
                document.location.href='index.php';
            </script>
            ";
        }
    } else {
        echo "
        <script>
            alert('Error inserting data: {$connect->error}');
            document.location.href = 'index.php';
        </script>
        ";
    }
}
?>
