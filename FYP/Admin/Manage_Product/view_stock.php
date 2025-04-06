<?php
    include __DIR__ . '/../../connect_db/config.php';

    if(isset($_GET['id'])){
        $product_id= $_GET['id'];
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['edit_stock'])) {
            if (isset($_POST['stock_id'])) {
                $stock_id = $_POST['stock_id'];
                $stock = $_POST['stock'];

                $sql = "UPDATE stock SET stock = $stock, last_update_at = NOW() WHERE stock_id = $stock_id";
                $result = $conn->query($sql);

                if($result) {
                    echo "<script>alert('Stock updated successfully!'); window.history.back();</script>";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        } else {
            $product_size=$_POST["size"];
            $product_sku=$_POST["sku"];
            $stock=$_POST["stock"];

            $check_sql = "SELECT * FROM stock WHERE product_id = '$product_id' AND product_size = '$product_size'";
            $check_result = $conn->query($check_sql);

            if($check_result->num_rows > 0) {
                echo "<script>alert('This size already exists!'); window.history.back();</script>";
            } else {
                $sql="INSERT INTO stock (product_id,product_size,product_sku,stock)
                      VALUE ('$product_id','$product_size','$product_sku','$stock')";
                $result= $conn->query($sql);

                if($result){
                    echo "<script>alert('Add Successfully!'); window.history.back();</script>";
                } else {
                    echo "Error:" . $conn->error;
                }
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
    <link rel="stylesheet" href="view_stock.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

<div class="contain">
    <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>
    <div class="stock-container">
        <div class="productInfo">
            <h2 style="color: white; text-align:center;">Product Info:</h2>
            <?php
                $product_type = "";
                if(isset($_GET['id'])){
                    $product_id= $_GET['id'];
                    $sql= "SELECT product_name, product_img1, product_type FROM product WHERE product_id = $product_id";
                    $result= $conn->query($sql);

                    while($row=$result->fetch_assoc()){
                        echo '<div class="product">
                                <img src="../../upload/'.$row["product_img1"].'">
                                <p>'.$row["product_name"].'</p>
                              </div>';
                        $product_type = $row["product_type"];
                    }
                }
            ?>
        </div>

        <div class="stock">
            <h1>STOCK:</h1>
            <div id="add-btn">
                <a id="add"><i class="fa-solid fa-plus" style="margin: 5px;"></i>Add More Size</a>
            </div>
            <table>
                <tr>
                    <th>Size</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Update Date</th>
                    <th></th>
                </tr>
                <?php
                    if(isset($_GET['id'])){
                        $product_id= $_GET['id'];
                        $sql="SELECT* FROM stock WHERE product_id=$product_id";
                        $result= $conn->query($sql);

                        while($row=$result->fetch_assoc()){
                            echo '<tr><td>'.$row["product_size"].'</td>
                                <td>'.$row["product_sku"].'</td>
                                <td>'.$row["stock"].'</td>
                                <td>'.$row["last_update_at"].'</td>
                                <td><div class="button">
                                    <a href="#" class="edit" id="edit" data-stock-id="'.$row["stock_id"].'" data-stock="'.$row["stock"].'">Edit</a>
                                    <a href="delete.php?stock_id='.$row["stock_id"].'" class="delete" id="delete" onclick="return confirm(\'Are you sure?\')">Delete</a>
                                </div></td>
                                </tr>';
                        }
                    }
                ?>
            </table>
        </div>

        <div class="addSize">
            <form action="" method="post">
                <h3>Add New Size:
                    <span id="close-btn">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </h3>
                <div class="column">
                    <div class="column-group">
                        <label for="">Size: </label>
                        <select name="size" id="size" required>
                            <option value="">Select Size</option>
                        </select>
                    </div>
                    <div class="column-group">
                        <label for="">SKU:</label>
                        <input type="text" name="sku" required>
                    </div>
                    <div class="column-group">
                        <label for="">Stock:</label>
                        <input type="number" name="stock" min="1" required>
                    </div>
                </div>
                <button type="submit" id="submit">Add</button>
            </form>
        </div>

        <div class="editSize" id="edit-stock">
            <form action="" method="post" id="edit-form">
                <h3>Edit Stock
                    <span id="edit-close-btn">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </h3>
                <div class="column">
                    <label for="stock">Stock:</label>
                    <input type="number" name="stock" id="edit-stock-input" min="1" required>
                </div>
                <input type="hidden" name="edit_stock" value="1">
                <input type="hidden" name="stock_id" id="edit-stock-id">
                <button type="submit">Update Stock</button>
            </form>
        </div>
    </div>
</div>

<script>
    const sizeOptions = {
        Footwear: ["UK13.5C", "UK1Y", "UK1.5Y", "UK2Y", "UK2.5Y", "UK3Y","UK3.5Y","UK4Y","UK4.5Y","UK5Y","UK5.5Y","UK6Y","UK6.5Y","UK3.5","UK4","UK4.5","UK5","UK5.5","UK6","UK6.5","UK7","UK7.5","UK8","UK8.5","UK9","UK9.5","UK10","UK10.5","UK11","UK11.5","UK12"],
        Apparel: ["XXS","XS","S", "M", "L","XL","2XL","3XL","4XL"],
        Equipment: ["One Size"]
    };

    function updateSizeOptions() {
        let productType = "<?php echo $product_type; ?>"; 
        let sizeSelect = document.getElementById("size");

        sizeSelect.innerHTML = ""; 
        if (sizeOptions[productType]) {
            sizeOptions[productType].forEach(size => {
                let option = document.createElement("option");
                option.value = size;
                option.textContent = size;
                sizeSelect.appendChild(option);
            });
        }
    }
    updateSizeOptions();

    document.addEventListener("DOMContentLoaded", function(){
        let add= document.getElementById("add-btn");
        let close= document.getElementById("close-btn");
        let popup = document.querySelector(".addSize");

        add.addEventListener("click", function(){
            popup.style.opacity="1";
            popup.style.visibility="visible";
        });

        close.addEventListener("click",function(){
            popup.style.opacity="0";
            popup.style.visibility="hidden";
        });
    });

    const editPopup = document.getElementById("edit-stock");
    const editClose = document.getElementById("edit-close-btn");

    document.querySelectorAll(".edit").forEach(function(btn) {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            let stockId = btn.getAttribute("data-stock-id");
            let stock = btn.getAttribute("data-stock");

            document.getElementById("edit-stock-input").value = stock;
            document.getElementById("edit-stock-id").value = stockId;

            editPopup.style.opacity = "1";
            editPopup.style.visibility = "visible";
        });
    });

    if (editClose) {
        editClose.addEventListener("click", function(){
            editPopup.style.opacity = "0";
            editPopup.style.visibility = "hidden";
        });
    }
</script>
</body>
</html>
