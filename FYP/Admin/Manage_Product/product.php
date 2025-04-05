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
                                <option value="nike">Nike</option>
                                <option value="adidas">Adidas</option>
                                <option value="puma">Puma</option>
                                <option value="asics">Asics</option>
                                <option value="underamour">Under Amour</option>
                                <option value="skechers">Skechers</option>
                                <option value="umbro">Umbro</option>
                                <option value="lotto">Lotto</option>
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
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Kid">Kid</option>
                        </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status"required>
                            <option value="Normal">Normal</option>
                            <option value="New">New</option>
                            <option value="Promotion">Promotion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price: </label>
                        <input type="number" name="price"required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price: </label>
                        <input type="number" name="discount_price">
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group" style="flex: 1;">
                        <label>Description:</label>
                        <textarea type="text" name="description" required style="height:150px; margin-left: 10px; border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Main Image 1:</label>
                        <input type="file" name="product_image1" required>
                    </div>
                    <div class="form-group">
                        <label>Image 2:</label>
                        <input type="file" name="product_image2">
                    </div>
                    <div class="form-group">
                        <label>Image 3:</label>
                        <input type="file" name="product_image3">
                    </div>
                    <div class="form-group">
                        <label>Image 4:</label>
                        <input type="file" name="product_image4">
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

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const categoryByType={
                "Footwear": ["Boot","Futsal","Running","Court","Training","Football Shoes","Kid Shoes","School Shoes"],
                "Apparel": ["Jersey","Jacket","Paint","Legging"],
                "Equipment": ["Bag","Cap","Football Accessories","Socks","Gym Accessories"]
            }

            const productType= document.getElementById("product_type");
            const productCategory= document.getElementById('product_categories');

            for(let type in categoryByType){
                let option= document.createElement("option");

                option.value=type;
                option.textContent=type;
                productType.appendChild(option);
            }

            productType.addEventListener("change", function(){
                let type=this.value;
                productCategory.innerHTML ='<option value="">Select Category</option>';

                if(type in categoryByType){
                    categoryByType[type].forEach(category=>{
                        let option= document.createElement("option");

                        option.value=category;
                        option.textContent=category;
                        productCategory.appendChild(option);
                    });
                }
            });
        });
    </script>
</body>
</html>