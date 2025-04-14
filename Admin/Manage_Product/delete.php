<?php
include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $product_id=$_GET['id'];

        $sql="SELECT* FROM product WHERE product_id=$product_id";
        $result= $conn->query($sql);
        $row= $result->fetch_assoc();

        $image1= $row['product_img1'] ??'';
        $image2= $row['product_img2'] ??'';
        $image3= $row['product_img3'] ??'';
        $image4= $row['product_img4'] ??'';
        $image5= $row['size_chart'] ??'';

        $upload="../../upload/";

        if(!empty($image1) && file_exists($upload . $image1)){
            unlink($upload . $image1);
        }
        if(!empty($image2) && file_exists($upload . $image2)){
            unlink($upload . $image2);
        }
        if(!empty($image3) && file_exists($upload . $image3)){
            unlink($upload . $image3);
        }
        if(!empty($image4) && file_exists($upload . $image4)){
            unlink($upload . $image4);
        }
        if(!empty($image5) && file_exists($upload . $image5)){
            unlink($upload . $image5);
        }

        $sql_delete="DELETE FROM product WHERE product_id=$product_id";
        $result_delete= $conn->query($sql_delete);
        $row_delete= $result->fetch_assoc();

        if($result_delete)
        {
            echo '<script>alert("Delete Successfully"); window.location.href="view_product.php"</script>';
        }else{
            echo "Error: " . $conn->error;
        }
    }

?>