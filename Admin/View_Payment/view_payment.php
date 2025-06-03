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
    <link rel="stylesheet" href="view_payment.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

    <?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="items-table">
            <h3>View Payment Status</h3>
            <div class="search-container">
                    <div class="search-box">
                        <form id="searchForm">
                            <input type="text" name="query" id="searchInput" placeholder="Enter ID For Search...">
                            <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                        
                        <button id="sortTime" onclick="triggerSort()">Sort by Time</button>
                    </div>
                </div>
            <table>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>View Order Items</th>
                    <th>Total Amount</th>
                    <th>Payment Time</th>
                    <th>Payment Status</th>
                </tr>
                
                <tbody id="userTableBody">
                <?php
                    $sql= "SELECT* FROM payment";
                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '
                            <tr>
                                <td>'.$row['payment_id'].'</td>
                                <td>'.$row['order_id'].'</td>
                                <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="view_item">View</a></td>
                                <td style="font-weight: bold;"> RM '.$row['total_amount'].'</td>
                                <td>'.$row['payment_at'].'</td>
                                <td><span style="background-color:rgb(35, 161, 31); color: white; padding:10px; border-radius:10px; font-weight: bold;">'.$row['payment_status'].'</span></td>
                            </tr>';
                    }
                    ?>
                    </tbody>
                </a>
            </table>
                
        </div>

        
    </div>

    <script>
        let timeSortAsc = true;
        let originalRows=[];
        let tbody;

        document.addEventListener('DOMContentLoaded',function(){
            tbody=document.getElementById('userTableBody');
            originalRows=Array.from(tbody.rows);
        });

        function triggerSort(){

            originalRows.sort((a,b)=>{
                const timeA= new Date(a.children[4].textContent.trim());
                const timeB= new Date(b.children[4].textContent.trim());
                return timeSortAsc ? timeB-timeA : timeA-timeB;
            });

            originalRows.forEach(row => tbody.appendChild(row));
            
            timeSortAsc = !timeSortAsc;
            document.getElementById("sortTime").innerHTML = timeSortAsc ? "Sort by Time " : "Sort by Time ▲";

        }
        
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault(); 

            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            fetch('search_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('userTableBody').innerHTML = html;
                originalRows = Array.from(document.getElementById('userTableBody').rows);
            }); 
        });
    </script>
</body>
</html>