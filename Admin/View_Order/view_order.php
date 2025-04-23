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
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Recipient Name</th>
                        <th>Recipient Phone Number</th>
                        <th>Total Price</th>
                        <th>Shipping Address</th>
                        <th>Delivery Status</th>
                        <th>Order Time</th>
                        <th>Update Status</th>
                    </tr>

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
                                    $statusColor = "background-color: #DC3545; font-weight: bold; padding: 6px 20px; border-radius: 4px;";
                                    break;
                                case "packing":
                                    $statusColor = "background-color:rgb(255, 128, 25); font-weight: bold; padding: 6px 10px; border-radius: 4px;";
                                    break;
                                case "assign":
                                    $statusColor = "background-color: #FFC107; font-weight: bold; padding: 6px 10px; border-radius: 4px;";
                                    break;
                                case "shipping":
                                    $statusColor = "background-color:rgb(0, 255, 64); font-weight: bold; padding: 6px 10px; border-radius: 4px;";
                                    break;
                                case "delivered":
                                    $statusColor = "background-color:rgb(7, 196, 0); font-weight: bold; padding: 6px 10px; border-radius: 4px;" ;
                                    break;
                                default:
                                    $statusColor = "background-color: red; font-weight: bold; padding: 6px 10px; border-radius: 4px;";
                                    break;
                            }

                            echo '<tr>
                                    <td>'. $row["order_id"].'</td>
                                    <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                                    <td>'.$row['mobile_number'].'</td>
                                    <td style="font-weight:bold;";>RM '. $row["total_price"].'</td>
                                    <td>'. $row["shipping_address"].'</td>
                                    <td><span style="'.$statusColor.'";>'. $row["delivery_status"].'</td>
                                    <td>'. $row["order_at"].'</td>
                                    <td>';
                                    if (strtolower($row["delivery_status"]) === "delivered") {
                                        echo '<span style="color: green; font-weight: bold;">Complete</span>';
                                    }else {
                                        echo '<a href="#" class="update" order_id="'.$row['order_id'].'">Update <i class="fa-solid fa-arrow-right"></i></a>';
                                    }
                                
                                    echo    '</td>
                                        </tr>';
                        }
                    ?>
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

    document.querySelectorAll(".update").forEach(function(btn) {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            let orderID = btn.getAttribute("order_id");

            document.getElementById("order-id").value = orderID;
            document.getElementById("order").textContent= orderID;
            
            Popup.style.opacity = "1";
            Popup.style.visibility = "visible";
        });
    });

    if (Close) {
        Close.addEventListener("click", function(){
            Popup.style.opacity = "0";
            Popup.style.visibility = "hidden";
        });
    }
  </script>
</body>
</html>