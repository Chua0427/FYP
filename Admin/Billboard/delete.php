<?php
    include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $Billboard_id=$_GET['id'];
    }

    $sql_image="SELECT image FROM billboard WHERE billboard_id= $Billboard_id";
    $result_image= $conn->query($sql_image);
    $row = $result_image->fetch_assoc();
    $old_profile_image = $row['image'] ?? '';
    $upload="../../upload/";


    $sql="DELETE FROM billboard WHERE billboard_id=$Billboard_id";
    $result= $conn->query($sql);
    

    if($result){
        echo "<script>alert('Delete Successfully!'); window.location.href='billboard.php';</script>";
        if(!empty($old_profile_image) && file_exists($upload . $old_profile_image))
        {
            unlink($upload . $old_profile_image);
        }
    }
    else{
        echo "Error" . $conn->error;
    }
?>