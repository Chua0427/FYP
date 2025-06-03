<?php
declare(strict_types=1);
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "verosports";

header('Content-Type: application/json');

try {
    $connect = new mysqli($servername, $username, $password, $dbname);
    
    if ($connect->connect_error) {
        throw new Exception("Connection failed: " . $connect->connect_error);
    }

    if (!isset($_SESSION['reset_email'])) {
        echo json_encode(['success' => false, 'message' => 'Session expired']);
        exit();
    }

    $email = $_SESSION['reset_email'];
    $otp = rand(100000, 999999);
    $expiry_time = date("Y-m-d H:i:s", strtotime("+15 minutes"));
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // 删除旧OTP
    $stmt = $connect->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // 插入新OTP
    $stmt = $connect->prepare("INSERT INTO password_resets (email, otp, ip, expiry_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $email, $otp, $ip_address, $expiry_time);
    
    if ($stmt->execute()) {
        // 发送邮件逻辑（与send_password_reset.php相同）
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';
        
        $mail = new PHPMailer(true);
        try {
            // ... 邮件配置与send_password_reset.php相同 ...
            $mail->Body = "..."; // 使用相同模板
            $mail->send();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>