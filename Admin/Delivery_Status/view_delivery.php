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
    <link rel="stylesheet" href="delivery_status.css">
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
                <h3>View Delivery Status</h3>
                <div class="search-container">
                    <div class="search-box">
                        <form id="searchForm">
                            <input type="text" name="query" id="searchInput" placeholder="Enter ID For Search...">
                            <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form> 

                        <button id="sortDeliveryStatus" onclick="triggerSort()">Sort by Status</button>
                    </div>
                </div>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>View Order Items</th>
                        <th>Recipient</th>
                        <th>Recipient Phone</th>
                        <th>Shipping Address</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>

                    <tbody id="userTableBody">
                        <?php
                            include __DIR__ . '/../../connect_db/config.php';

                            $sql= "SELECT o.*,u.first_name,u.last_name,u.mobile_number FROM orders o
                                JOIN users u ON o.user_id= u.user_id";
                            $result= $conn->query($sql);

                            while($row= $result->fetch_assoc()){
                                $orderId = $row["order_id"];
                                $statusColor = "";
                                $status= $row['delivery_status'];

                                switch ($status) {
                                    case "prepare":
                                        $statusColor = "background-color: #DC3545; font-weight: bold; padding: 8px 16px; border-radius: 4px; color:white;";
                                        break;
                                    case "packing":
                                        $statusColor = "background-color:rgb(255, 128, 25); font-weight: bold; padding: 8px 15px; border-radius: 4px; color:white;";
                                        break;
                                    case "assign":
                                        $statusColor = "background-color: #FFC107; font-weight: bold; padding: 8px 20px; border-radius: 4px; color:white;";
                                        break;
                                    case "shipping":
                                        $statusColor = "background-color:rgb(0, 199, 50); font-weight: bold; padding: 8px 12px; border-radius: 4px; color:white;";
                                        break;
                                    case "delivered":
                                        $statusColor = "color:rgb(7, 196, 0); font-weight: bold; padding: 8px 10px; border-radius: 4px;" ;
                                        break;
                                    default:
                                        $statusColor = "background-color: red; font-weight: bold; padding: 8px 10px; border-radius: 4px;";
                                        break;
                                }

                                echo '<tr>
                                        <td>'. $row["order_id"].'</td>
                                        <td><a href="../View_Order_Items/view_order_item.php?order_id='.$row['order_id'].'" class="items">View</a></td>
                                        <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                                        <td>'.$row['mobile_number'].'</td>
                                        <td>'. $row["shipping_address"].'</td>
                                        <td><span style="'.$statusColor.'";>'. $row["delivery_status"].'</td>
                                        <td>';
                                        if (strtolower($row["delivery_status"]) === "delivered") {
                                            echo '<span style="color: green; font-weight: bold;">Complete</span>';
                                        }else {
                                            echo '<a href="#" class="update" order_id="'.$row['order_id'].'">Update</a>';
                                        }
                                    
                                        echo    '</td>
                                            </tr>';
                            }
                        ?>
                    </tbody>
                </table>

                <div id="updateStatus">
                    <form action="delivery_status.php" method="post">
                        <h3>Update Delivery Status:
                            <span id="close-btn">
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </h3>
                        <div class="column">
                            <p>Order ID: 
                                <span id="order"></span>
                            </p>
                            <div class="deliveryStatus">
                                <button class="status-button" name="status" value="prepare">Prepare</button>
                                <button class="status-button" name="status" value="packing">Packing</button>
                                <button class="status-button" name="status" value="assign">Assign</button>
                                <button class="status-button" name="status" value="shipping">Shipping</button>
                                <button class="status-button" id="delivered" name="status" value="delivered">Delivered</button>
                            </div>
                            

                        <input type="hidden" id="order-id" name="order_id">

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

<script>
    const Popup = document.getElementById("updateStatus");
    const Close = document.getElementById("close-btn");

    document.getElementById("userTableBody").addEventListener("click", function(e) {
        if (e.target.classList.contains("update")) {
            e.preventDefault();
            let orderID = e.target.getAttribute("order_id");

            document.getElementById("order-id").value = orderID;
            document.getElementById("order").textContent = orderID;

            Popup.style.opacity = "1";
            Popup.style.visibility = "visible";
        }
    });

    if (Close) {
        Close.addEventListener("click", function(){
            Popup.style.opacity = "0";
            Popup.style.visibility = "hidden";
        });
    }


    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const form = document.getElementById('searchForm');
        const formData = new FormData(form);

        fetch('search_delivery.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('userTableBody').innerHTML = html;

            originalRows = Array.from(document.querySelector('#userTableBody').rows);
            sortState = false;
        }); 
    });

    
    let originalRows;
    let tbody;
    let sorted=false;

    document.addEventListener('DOMContentLoaded', function(){
        tbody=document.getElementById("userTableBody");
        originalRows=Array.from(tbody.rows);
    });

    function triggerSort() {
            
        if(!sorted){
            const rows = Array.from(tbody.rows);

            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[5].innerText.trim();  
                let cellB = rowB.cells[5].innerText.trim();

                let cleanCellA = cellA.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();
                let cleanCellB = cellB.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();

                    return cleanCellA.localeCompare(cleanCellB);  
                
            });

            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));

            document.getElementById("sortDeliveryStatus").innerHTML = "Sort by Status ▲";
            sorted=true;
        }else{
            tbody.innerHTML = '';
            originalRows.forEach(row => tbody.appendChild(row));
            document.getElementById("sortDeliveryStatus").innerHTML = "Sort by Status";
            sorted = false;
        }
            
    }


</script>

</body>
</html>