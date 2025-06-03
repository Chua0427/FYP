<?php


require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

if(isset($_GET['id']))
{
    $admin_id= $_GET['id'];
    $old_image= "SELECT profile_image FROM users WHERE user_id= $admin_id";
    $result= $conn->query("$old_image");
    $row = $result->fetch_assoc();
    $old_profile_image = $row['profile_image'] ?? '';

    $upload= "../../upload/";
    if(!empty($old_profile_image) && file_exists($upload . $old_profile_image))
    {
        unlink($upload . $old_profile_image);
    }

    $sql= "DELETE FROM users WHERE user_id= $admin_id";
    $user= $conn->query($sql);

    if($user){
        echo "<script>alert('Delete Successfully!'); window.location.href='view_admin.php';</script>";
    }
    else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>