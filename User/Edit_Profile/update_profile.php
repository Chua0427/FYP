<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $data = [
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'mobile_number' => filter_input(INPUT_POST, 'mobile_number', FILTER_SANITIZE_STRING),
        'birthday_date' => filter_input(INPUT_POST, 'birthday_date', FILTER_SANITIZE_STRING),
        'gender' => filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING),
        'user_id' => $_SESSION['user_id']
    ];

    // Update database
    $sql = "UPDATE users SET 
            first_name = :first_name,
            last_name = :last_name,
            mobile_number = :mobile_number,
            birthday_date = :birthday_date,
            gender = :gender,
            updated_at = NOW()
            WHERE user_id = :user_id";

    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute($data)) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile: " . implode(" ", $stmt->errorInfo());
    }
    
    header("Location: profile.php");
    exit();
}
?>