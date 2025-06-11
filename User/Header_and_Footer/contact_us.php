<?php
    require_once __DIR__ . '/../auth_check.php';
    include __DIR__ . '/../../connect_db/config.php';
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if admin in view-only mode
    if (isset($_SESSION['admin_view_only']) && $_SESSION['admin_view_only'] === true) {
        // Store the current URL for the redirect back
        $_SESSION['admin_redirect_from'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to admin notification page
        header('Location: /FYP/User/admin_notification.php');
        exit;
    }
    
    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if(!$user){
        echo "<script>alert('Please Sign Up and Login First! Thank You!'); window.location.href='../login/login.php';</script>";
        exit;
    }
    else{
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $user_id=$user;
            $message= $_POST['message'];

            $sql= "INSERT INTO contact_us (user_id,message)
                    VALUE ('$user_id','$message')";
            $result= $conn->query($sql);

            if($result){
                echo "<script>alert('Submit Message Successfully! Please be patient that our customer supprt will reply in email as soon as possible. Thank you for contact us!'); history.back();</script>";
            }else{
                echo "Error: " . $conn->error;
            }
        }
    }
?>