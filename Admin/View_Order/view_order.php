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
    <link rel="stylesheet" href="view_order.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="order-container">
            <div class="order-table">
                <h3>View Order</h3>
                <div class="search-container">
                    <div class="search-box">
                        <form id="searchForm">
                            <input type="text" name="query" id="searchInput" placeholder="Enter ID Or Name For Search...">
                            <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form> 

                        <button id="sortTime" onclick="triggerSort()">Sort by Time</button>
                    </div>
                </div>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th></th>
                        <th>Customer Name</th>
                        <th>Customer Phone Number</th>
                        <th>Total Price</th>
                        <th>Order Time</th>
                        <th>View Order Items</th>
                    </tr>
                    
                    <tbody id="userTableBody">
                        <?php
                            include __DIR__ . '/../../connect_db/config.php';

                            $sql= "SELECT o.*,u.first_name,u.last_name,u.mobile_number, u.profile_image FROM orders o
                                JOIN users u ON o.user_id= u.user_id";
                            $result= $conn->query($sql);

                            while($row= $result->fetch_assoc()){
                                $orderId = $row["order_id"];
                                $image = !empty($row['profile_image']) ? $row['profile_image'] : 'default.jpg';
                                echo '<tr>
                                        <td>'. $row["order_id"].'</td>
                                        <td><img src="../../upload/'.$image.'"</td>
                                        <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                                        <td>'.$row['mobile_number'].'</td>
                                        <td style="font-weight:bold;";>RM '. $row["total_price"].'</td>
                                        <td>'. $row["order_at"].'</td>
                                        <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="items">View</a></td>
                                    </tr>';
                            }
                        ?>
                    </tbody>
                </table>

            </div>
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
                const timeA= new Date(a.children[5].textContent.trim());
                const timeB= new Date(b.children[5].textContent.trim());
                return timeSortAsc ? timeB-timeA : timeA-timeB;
            });

            originalRows.forEach(row => tbody.appendChild(row));
            
            timeSortAsc = !timeSortAsc;
            document.getElementById("sortTime").innerHTML = timeSortAsc ? "Sort by Time" : "Sort by Time ▲";

        }

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault(); 

            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            fetch('search_order.php', {
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