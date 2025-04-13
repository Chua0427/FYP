<?php
    include __DIR__ . '/../../connect_db/config.php';
    //session_start(); 
    $user=25;


    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$user;
        $message= $_POST['message'];

        $sql= "INSERT INTO contact_us (user_id,message)
                VALUE ('$user_id','$message')";
        $result= $conn->query($sql);

        if($result){
            echo "<script>alert('Submit Message Successfully! Please be patient that our customer supprt will reply in email as soon as possible. Thank tou for contact us!'); history.back();</script>";
        }else{
            echo "Error: " . $conn->error;
        }
        }
?>