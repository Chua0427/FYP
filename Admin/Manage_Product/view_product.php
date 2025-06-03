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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_product.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="product-container">
            <div class="product-table">
                <h3>View Product</h3>
                <div class="search-container">
                    <div class="search-box">
                        <form id="searchForm">
                            <input type="text" name="query" id="searchInput" placeholder="Enter Keyword For Search...">
                            <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form> 
                    </div>
                </div>

                <div class="add-sort">
                    <div class="add-btn">
                        <a href="product.php" id="add"><i class="fa-solid fa-plus" style="margin: 5px;"></i>Add More</a>
                    </div>
                    <div class="sort-buttons">
                        <button id="sortname" onclick="triggerSort(2)">Sort A-Z</button>
                        <button id="sortprice" onclick="triggerSort(7)">Sort by Price</button>
                        <button onclick="resetTable()">Reset</button>
                    </div>
                </div>
                
                <table>
                    <tr>
                        <th>Product ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Price (RM)</th>
                        <th></th>
                    </tr>

                    <tbody id="userTableBody">
                        <?php
                            include __DIR__ . '/../../connect_db/config.php';

                            $sql = "SELECT * FROM product WHERE deleted=0";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $finalPrice = $row["discount_price"] > 0 ? $row["discount_price"] : $row["price"];
                                echo '<tr>
                                        <td>'. $row["product_id"].'</td>
                                        <td><img src="../../upload/'. $row["product_img1"].'"</td>
                                        <td>'. $row["product_name"].'</td>
                                        <td style="color:orangered; font-weight:bold;">'. $row["brand"].'</td>
                                        <td>'. $row["product_categories"].'</td>
                                        <td>'. $row["gender"].'</td>
                                        <td>'. $row["status"].'</td>
                                        <td style="color:red; font-weight:bold;">'. number_format($finalPrice, 2).'</td>
                                        <td><div class="button"><a href="view_stock.php?id='.$row["product_id"].'" class="stock-button"><i class="fa-solid fa-boxes-packing"></i></a>
                                        <a href="edit.php?id='.$row["product_id"].'" id="edit"><i class="fa-solid fa-pen"></i></a>
                                        <a href="delete.php?id='.$row["product_id"].'" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a></div></td>
                                    </tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
    let currentSortColumn = null;
    let currentSortOrder = 'asc';
    let originalRows = [];

        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.querySelector('#userTableBody');
            originalRows = Array.from(tbody.rows); 
        });

        function triggerSort(column) {

            document.getElementById('sortname').innerHTML = 'Sort A-Z';
            document.getElementById('sortprice').innerHTML = 'Sort by Price';

            if (column === 2) {  
                if (currentSortColumn === column.toString()) {
                    currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSortOrder = 'asc';
                }
                currentSortColumn = column.toString();
                sortByName(currentSortOrder);
                const arrow = currentSortOrder === 'asc' ? ' ▲' : ' ▼';
                document.getElementById('sortname').innerHTML += arrow;
            } else if (column === 7) {  
                if (currentSortColumn === column.toString()) {
                    currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSortOrder = 'asc';
                }
                currentSortColumn = column.toString();
                sortByPrice(currentSortOrder);
                const arrow = currentSortOrder === 'asc' ? ' ▲' : ' ▼';
                document.getElementById('sortprice').innerHTML += arrow;
            }
        }


        function resetTable() {
            const tbody = document.querySelector('#userTableBody');
            tbody.innerHTML = '';
            originalRows.forEach(row => tbody.appendChild(row));
            currentSortColumn = null;
            currentSortOrder = 'asc';

            document.getElementById('sortname').innerHTML = 'Sort A-Z';
            document.getElementById('sortprice').innerHTML = 'Sort by Price';
        }

        function sortByName(order) {
            const tbody = document.querySelector('#userTableBody');
            const rows = Array.from(tbody.rows);

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[2].innerText.trim();  
                let cellB = rowB.cells[2].innerText.trim();

                let cleanCellA = cellA.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();
                let cleanCellB = cellB.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();

                if (order === 'asc') {
                    return cleanCellA.localeCompare(cleanCellB);  
                } else {
                    return cleanCellB.localeCompare(cleanCellA);  
                }
            });

            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }

        function sortByPrice(order) {
            const tbody = document.querySelector('#userTableBody');
            const rows = Array.from(tbody.rows);

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[7].innerText.trim();  
                let cellB = rowB.cells[7].innerText.trim();

                
                let valueA = parseFloat(cellA.replace(/[^0-9.-]/g, ''));
                let valueB = parseFloat(cellB.replace(/[^0-9.-]/g, ''));

                
                if (isNaN(valueA)) valueA = 0;
                if (isNaN(valueB)) valueB = 0;

                return order === 'asc' ? valueA - valueB : valueB - valueA;
            });

            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }



        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('search_product.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
                .then(html => {
                    document.getElementById('userTableBody').innerHTML = html;
                    originalRows = Array.from(document.querySelector('#userTableBody').rows); 
                });
        });
</script>
</body>
</html>
