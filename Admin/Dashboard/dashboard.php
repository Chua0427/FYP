<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

    <?php 
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        include __DIR__ . '/../../connect_db/config.php'; 

        //today sale
        $todaySales=0;
        $today=date('y-m-d');

        $sqlTodaySale="SELECT SUM(total_amount) AS total FROM payment WHERE DATE(payment_at) = '$today' AND payment_status='completed'";

        $todaySaleResult= $conn->query($sqlTodaySale);

        while($row= $todaySaleResult->fetch_assoc()){
            $todaySales=$row['total'] ?? 0;
        }

        //users
        $sqlUser= "SELECT COUNT(*) AS total_user FROM users WHERE user_type=1";

        $userResult= $conn->query($sqlUser);
        while($row= $userResult->fetch_assoc()){
            $totalUser= $row['total_user'];
        }

        //total order
        $sqlOrder="SELECT COUNT(*) AS total_order FROM orders";

        $orderResult= $conn->query($sqlOrder);
        while($row= $orderResult->fetch_assoc()){
            $totalOrder= $row['total_order'];
        }

        //total sale
        $totalSales=0;

        $sqlTotalSale="SELECT SUM(total_amount) AS total_sales FROM payment WHERE payment_status='completed'";

        $totalSaleResult= $conn->query($sqlTotalSale);
        while($row= $totalSaleResult->fetch_assoc()){
            $totalSales+= $row['total_sales'] ?? 0;
        }

        $sqlChart = "
        SELECT 
        (SELECT COUNT(DISTINCT p.product_id) 
         FROM product p JOIN stock s ON p.product_id = s.product_id 
         WHERE p.status = 'Normal' AND s.stock > 0) AS normal_count,

        (SELECT COUNT(DISTINCT p.product_id) 
         FROM product p JOIN stock s ON p.product_id = s.product_id 
         WHERE p.status = 'Promotion' AND s.stock > 0) AS promotion_count,

        (SELECT COUNT(DISTINCT p.product_id) 
         FROM product p JOIN stock s ON p.product_id = s.product_id 
         WHERE p.status = 'New' AND s.stock > 0) AS new_count
        ";

        $resultChart = $conn->query($sqlChart);
        $rowchart = $resultChart->fetch_assoc();

        // Get sales data for the last 7 days
        $salesData = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = $date;

            $sql = "SELECT SUM(total_amount) AS total FROM payment 
                    WHERE DATE(payment_at) = '$date' AND payment_status = 'completed'";

            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $salesData[] = $row['total'] ?? 0;
        }

?>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="dashboard">
            <div class="column">
                <div class="icon">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <h3>Today's Sales</h3>
                <p class="sales">+ RM <?php echo number_format($todaySales,2) ?></p>
            </div>

            <div class="column">
                <div class="icon" >
                    <i class="fa-solid fa-circle-user" style="background-color: rgb(108, 108, 255);"></i>
                </div>
                <h3>Users</h3>
                <p class="sales"><?php echo $totalUser ?></p>
            </div>
            <div class="column">
                <div class="icon">
                    <i class="fa-solid fa-box" style="background-color: #ffbb00;"></i>
                </div>
                <h3>Total Order</h3>
                <p class="sales"><?php echo $totalOrder ?></p>
            </div>
            
            <div class="column">
                <div class="icon" >
                    <i class="fa-solid fa-arrow-up-wide-short" style="background-color: rgb(0, 196, 0);"></i>
                </div>
                <h3>Total Sales</h3>
                <p class="sales">RM <?php echo number_format($totalSales,2) ?></p>
            </div>
    </div>
</div>

<div class="sub-dashboard">
    <div class="top-sale">
        <h2>Sales in Last 7 Days</h2>
        <canvas id="weeklySalesChart" width="450" height="330"></canvas>
    </div>


    <div class="sub-container">
        <div class="chart">
            <canvas id="myChart" width="420" height="330"></canvas>
        </div>
    </div>



    <?php
        $user_id = $_SESSION['user_id'];
        
        $sql= "SELECT * FROM users WHERE user_id = $user_id";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo '<div class="admin">
                <img src="../../upload/' . $row['profile_image'] . '" alt="Profile Image">
                <p>Welcome Back ! '.$row['first_name'].'</p>
                <p>Email: '.$row['email'].'</p>
                <a href="../../User/login/logout.php" ><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>';
        }

    ?>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',  
        data: {
            labels: ['Normal', 'Promotion', 'New Arrivals'], 
            datasets: [{
                data: <?php echo '['.$rowchart['normal_count'].', '.$rowchart['promotion_count'].', '.$rowchart['new_count'].']'; ?>, 
                backgroundColor: ['#007bff', '#dc3545', '#ffc107'], 
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    //sales for last 7 days
    var ctx = document.getElementById('weeklySalesChart').getContext('2d');
    var weeklySalesChart = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Sales (RM)',
                data: <?php echo json_encode($salesData); ?>,
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount (RM)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>
    


</body>

</html>