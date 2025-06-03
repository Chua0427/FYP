<?php
    

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_name= $_POST["product_name"];
        $product_type= $_POST["product_type"];
        $brand= $_POST["brand"];
        $product_category= $_POST["product_categories"];
        $gender= $_POST["gender"];
        $status= $_POST["status"];
        $price= $_POST["price"];
        $discount_price = isset($_POST["discount_price"]) && $_POST["discount_price"] !== '' ? $_POST["discount_price"] : 0;

        $description= $_POST["description"];
        $deleted=0;

        $upload= "../../upload/";

        $originalName1= $_FILES["product_image1"]["name"];
        $fileExtension1= pathinfo($originalName1, PATHINFO_EXTENSION);
        $uniqueName1= uniqid() . "." . $fileExtension1;
        $targetPath1= $upload . $uniqueName1;

        $originalName2= $_FILES["product_image2"]["name"];
        $fileExtension2= pathinfo($originalName2, PATHINFO_EXTENSION);
        $uniqueName2= uniqid() . "." . $fileExtension2;
        $targetPath2= $upload . $uniqueName2;

        $originalName3= $_FILES["product_image3"]["name"];
        $fileExtension3= pathinfo($originalName3, PATHINFO_EXTENSION);
        $uniqueName3= uniqid() . "." . $fileExtension3;
        $targetPath3= $upload . $uniqueName3;

        $originalName4= $_FILES["product_image4"]["name"];
        $fileExtension4= pathinfo($originalName4, PATHINFO_EXTENSION);
        $uniqueName4= uniqid() . "." .$fileExtension4;
        $targetPath4= $upload . $uniqueName4;

        $originalName= $_FILES["size_chart"]["name"];
        $fileExtension= pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName= uniqid() . "." .$fileExtension;
        $targetPath= $upload . $uniqueName;

        $sql = "INSERT INTO product (
            product_name, 
            product_type, 
            brand, 
            product_categories, 
            gender, 
            status, 
            price, 
            discount_price, 
            description, 
            product_img1, 
            product_img2, 
            product_img3, 
            product_img4, 
            size_chart,
            deleted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param(
            "ssssssddssssssi", 
            $product_name,
            $product_type,
            $brand,
            $product_category,
            $gender,
            $status,
            $price,
            $discount_price,
            $description,
            $uniqueName1,
            $uniqueName2,
            $uniqueName3,
            $uniqueName4,
            $uniqueName,
            $deleted
        );
        
        $result = $stmt->execute();

        if($result){
            move_uploaded_file($_FILES['product_image1']['tmp_name'], $targetPath1);
            move_uploaded_file($_FILES['product_image2']['tmp_name'], $targetPath2);
            move_uploaded_file($_FILES['product_image3']['tmp_name'], $targetPath3);
            move_uploaded_file($_FILES['product_image4']['tmp_name'], $targetPath4);
            move_uploaded_file($_FILES['size_chart']['tmp_name'], $targetPath);
            echo "<script>alert('Add Successfully!'); window.location.href='view_product.php';</script>";
        }
        else{
            echo "Error: " . $conn->error;
        }
    }
?>