<?php
    

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

    if($_SERVER ["REQUEST_METHOD"]=="POST"){

        $upload="../../upload/";

        $originalName=$_FILES["billboard_img"]["name"];
        $extension= pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName=time().".".$extension;
        $path= $upload . $uniqueName;

        $sql= "INSERT INTO billboard (image)
               VALUE ('$uniqueName')";
        $result = $conn->query($sql);

        if($result){
            move_uploaded_file($_FILES['billboard_img']['tmp_name'], $path);
            echo "<script>alert('Add Successfully!'); window.location.href='billboard.php';</script>";
        }
        else{
            echo "Error" . $conn->error;
        }
    }
?>



