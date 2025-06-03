<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin (user_type = 2 or user_type = 3)
    if ($_SESSION['user_type'] != 2 && $_SESSION['user_type'] != 3) {
        // Redirect non-admin users to the main site
        header("Location: /FYP/FYP/User/HomePage/homePage.php");
        exit;
    }
    
include __DIR__ . '/../../connect_db/config.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM product WHERE product_id = $product_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_name= $_POST["product_name"];
        $product_type= $_POST["product_type"];
        $brand= $_POST["brand"];
        $product_category= $_POST["product_categories"];
        $gender= $_POST["gender"];
        $status= $_POST["status"];
        $price= $_POST["price"];
        $discount_price = isset($_POST["discount_price"]) && $_POST["discount_price"] !== '' ? $_POST["discount_price"] : 0;
        $description= $_POST["description"];

        $image1 = '';
        $image2 = '';
        $image3 = '';
        $image4 = '';
        $image5 = '';
        
        $sql_old_image1 = "SELECT product_img1 FROM product WHERE product_id = $product_id";
        $result_old_image1 = $conn->query($sql_old_image1);
        $row_old_image1 = $result_old_image1->fetch_assoc();
        $old_profile_image1 = $row_old_image1['product_img1'] ?? '';

        if (!empty($_FILES['image1']['name'])) {
            $upload = "../../upload/";
            $originalName = $_FILES['image1']['name'];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $upload . $uniqueFileName;

            if (!empty($old_profile_image1) && file_exists($upload . $old_profile_image1)) {
                unlink($upload . $old_profile_image1);
            }

            if (move_uploaded_file($_FILES['image1']['tmp_name'], $targetPath)) {
                $image1 = ", product_img1='$uniqueFileName'";
            }
        }

        $sql_old_image2 = "SELECT product_img2 FROM product WHERE product_id = $product_id";
        $result_old_image2 = $conn->query($sql_old_image2);
        $row_old_image2 = $result_old_image2->fetch_assoc();
        $old_profile_image2 = $row_old_image2['product_img2'] ?? '';

        if (!empty($_FILES['image2']['name'])) {
            $upload = "../../upload/";
            $originalName = $_FILES['image2']['name'];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $upload . $uniqueFileName;

            if (!empty($old_profile_image2) && file_exists($upload . $old_profile_image2)) {
                unlink($upload . $old_profile_image2);
            }

            if (move_uploaded_file($_FILES['image2']['tmp_name'], $targetPath)) {
                $image2 = ", product_img2='$uniqueFileName'";
            }
        }

        $sql_old_image3 = "SELECT product_img3 FROM product WHERE product_id = $product_id";
        $result_old_image3 = $conn->query($sql_old_image3);
        $row_old_image3 = $result_old_image3->fetch_assoc();
        $old_profile_image3 = $row_old_image3['product_img3'] ?? '';

        if (!empty($_FILES['image3']['name'])) {
            $upload = "../../upload/";
            $originalName = $_FILES['image3']['name'];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $upload . $uniqueFileName;

            if (!empty($old_profile_image3) && file_exists($upload . $old_profile_image3)) {
                unlink($upload . $old_profile_image3);
            }

            if (move_uploaded_file($_FILES['image3']['tmp_name'], $targetPath)) {
                $image3 = ", product_img3='$uniqueFileName'";
            }
        }

        $sql_old_image4 = "SELECT product_img4 FROM product WHERE product_id = $product_id";
        $result_old_image4 = $conn->query($sql_old_image4);
        $row_old_image4 = $result_old_image4->fetch_assoc();
        $old_profile_image4 = $row_old_image4['product_img4'] ?? '';

        if (!empty($_FILES['image4']['name'])) {
            $upload = "../../upload/";
            $originalName = $_FILES['image4']['name'];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $upload . $uniqueFileName;

            if (!empty($old_profile_image4) && file_exists($upload . $old_profile_image4)) {
                unlink($upload . $old_profile_image4);
            }

            if (move_uploaded_file($_FILES['image4']['tmp_name'], $targetPath)) {
                $image4 = ", product_img4='$uniqueFileName'";
            }
        }

        $sql_old_image5 = "SELECT size_chart FROM product WHERE product_id = $product_id";
        $result_old_image5 = $conn->query($sql_old_image5);
        $row_old_image5 = $result_old_image5->fetch_assoc();
        $old_profile_image5 = $row_old_image5['size_chart'] ?? '';

        if (!empty($_FILES['image5']['name'])) {
            $upload = "../../upload/";
            $originalName = $_FILES['image5']['name'];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $upload . $uniqueFileName;

            if (!empty($old_profile_image5) && file_exists($upload . $old_profile_image5)) {
                unlink($upload . $old_profile_image5);
            }

            if (move_uploaded_file($_FILES['image5']['tmp_name'], $targetPath)) {
                $image5 = ", size_chart='$uniqueFileName'";
            }
        }

        $sql="UPDATE product SET product_name = ?,product_type = ?,brand = ?,product_categories = ?,gender = ?, status = ?,price = ?,discount_price = ?,description = ? $image1 $image2 $image3 $image4 $image5 
              WHERE product_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
        "ssssssddsi",  $product_name,$product_type,$brand,$product_category,$gender,$status,$price,$discount_price,  $description,$product_id);

        $result = $stmt->execute();

        if ($result) {
            echo '<script>alert("Edit Successfully"); window.location.href="view_product.php"</script>';
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
            <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

            <div class="form-container">
            <h2>Edit Product</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="column1">
                    <div class="form-group">
                        <label>Product Name:</label>
                        <input type="text" name="product_name" value="<?php echo ($row['product_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Product Type:</label>
                            <select id="product_type" name="product_type" required>
                                <option value="<?= ($row['product_type']); ?>" selected><?= ($row['product_type']); ?></option>
                            </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Brand:</label>
                            <?php
                                $brands = ["Nike", "Adidas", "Puma", "Asics", "Under Amour", "New Balance", "Umbro", "Lotto"];
                                $current_brand = $row['brand'];
                            ?>      
                            
                            <select name="brand" required>
                                <option value="<?= ($row['brand']); ?>" selected><?= ($row['brand']); ?></option>
                                <?php foreach ($brands as $brand): ?>
                                    <?php if ($brand !== $current_brand): ?>
                                        <option value="<?= $brand ?>"><?= $brand ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label>Product Categories:</label>
                        <select id="product_categories" name="product_categories" required>
                            <option value="<?= ($row['product_categories']); ?>" selected><?= ($row['product_categories']); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender"required>
                            <option value="<?= ($row['gender']); ?>" selected><?= ($row['gender']); ?></option>
                            <?php 
                                    if($row['gender']== "Men"){
                                        echo '<option value="Women">Women</option>';
                                        echo '<option value="Kid">Kid</option>';
                                    }
                                    else if($row['gender']== "Women"){
                                        echo '<option value="Men">Men</option>';
                                        echo '<option value="Kid">Kid</option>';
                                    }
                                    else{
                                        echo '<option value="Men">Men</option>';
                                        echo '<option value="Women">Women</option>';
                                    }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" id="status" required>
                            <option value="<?= ($row['status']); ?>" selected><?= ($row['status']); ?></option>
                            <?php 
                                    if($row['status']== "Normal"){
                                        echo '<option value="New">New</option>';
                                        echo '<option value="Promotion">Promotion</option>';
                                    }
                                    else if($row['status']== "New"){
                                        echo '<option value="Normal">Normal</option>';
                                        echo '<option value="Promotion">Promotion</option>';
                                    }
                                    else{
                                        echo '<option value="Normal">Normal</option>';
                                        echo '<option value="New">New</option>';
                                    }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price: </label>
                        <input type="number" name="price" step="0.01" value="<?php echo ($row['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price: </label>
                        <input type="number" name="discount_price" id="discount_price" step="0.01" value="<?php echo ($row['discount_price']); ?>">
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group" style="flex: 1;">
                        <label>Description:</label>
                        <textarea name="description" style="height:150px; margin-left: 10px; border-radius: 8px;"><?php echo ($row['description']); ?></textarea>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Main Image 1:</label>
                        <input type="file" name="image1">
                    </div>
                    <div class="form-group">
                        <label>Image 2:</label>
                        <input type="file" name="image2">
                    </div>
                    <div class="form-group">
                        <label>Image 3:</label>
                        <input type="file" name="image3">
                    </div>
                    <div class="form-group">
                        <label>Image 4:</label>
                        <input type="file" name="image4">
                    </div>
                </div>

                <div class="form-group">
                    <label>Size Chart:</label>
                    <input type="file" name="image5">
                </div>

                <button type="submit" id="submit">Edit</button>
            </form>
        </div>
        </div>
        <script src="add.js"></script>
</body>
</html>

