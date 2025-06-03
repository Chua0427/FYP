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
    <?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="form-container">
            <h2>Add New Product</h2>
            <form action="add.php" method="POST" enctype="multipart/form-data">
                <div class="column1">
                    <div class="form-group">
                        <label>Product Name:</label>
                        <input type="text" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label>Product Type:</label>
                            <select id="product_type" name="product_type" required>
                                <option value="">Select Type</option>
                            </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Brand:</label>
                            <select name="brand" required>
                                <option value="Nike">Nike</option>
                                <option value="Adidas">Adidas</option>
                                <option value="Puma">Puma</option>
                                <option value="Asics">Asics</option>
                                <option value="Under Amour">Under Amour</option>
                                <option value="New Balance">New Balance</option>
                                <option value="Umbro">Umbro</option>
                                <option value="Lotto">Lotto</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label>Product Categories:</label>
                        <select id="product_categories" name="product_categories" required>
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender"required>
                            <option value="Men">Men</option>
                            <option value="Women">Women</option>
                            <option value="Kid">Kid</option>
                        </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" id="status" required>
                            <option value="Normal">Normal</option>
                            <option value="New">New</option>
                            <option value="Promotion">Promotion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price: </label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price: </label>
                        <input type="number" step="0.01" name="discount_price" id="discount_price">
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group" style="flex: 1;">
                        <label>Description:</label>
                        <textarea name="description" required style="height:150px; margin-left: 10px; border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Main Image 1:</label>
                        <input type="file" name="product_image1" required>
                    </div>
                    <div class="form-group">
                        <label>Image 2:</label>
                        <input type="file" name="product_image2" required>
                    </div>
                    <div class="form-group">
                        <label>Image 3:</label>
                        <input type="file" name="product_image3" required>
                    </div>
                    <div class="form-group">
                        <label>Image 4:</label> 
                        <input type="file" name="product_image4" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Size Chart:</label>
                    <input type="file" name="size_chart">
                </div>

                <button type="submit" id="submit">Add</button>
            </form>
        </div>
    </div>

    <script src="add.js"></script>

</body>
</html>