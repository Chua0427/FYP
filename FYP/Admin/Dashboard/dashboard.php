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

<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.html'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.html'; ?>

        <div class="dashboard">
            <div class="column">
                <div class="icon">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <h3>Today's Sales</h3>
                <p class="sales">+ RM 1000</p>
            </div>

            <div class="column">
                <div class="icon" >
                    <i class="fa-solid fa-circle-user" style="background-color: rgb(108, 108, 255);"></i>
                </div>
                <h3>Users</h3>
                <p class="sales">100</p>
            </div>
            <div class="column">
                <div class="icon">
                    <i class="fa-solid fa-box" style="background-color: #ffbb00;"></i>
                </div>
                <h3>Total Order</h3>
                <p class="sales">10</p>
            </div>
            
            <div class="column">
                <div class="icon" >
                    <i class="fa-solid fa-arrow-up-wide-short" style="background-color: rgb(0, 196, 0);"></i>
                </div>
                <h3>Total Sales</h3>
                <p class="sales">RM 10000</p>
            </div>
    </div>
</div>

<div class="sub-dashboard">
    <div class="top-sale">
        <h2>Top 3 Best Selling Products</h2>
        <div class="product-list">
            <div class="product">
                <img src="images/Screenshot 2025-03-24 124204.png" alt="Product 1">
                <div class="info">
                    <h3>Nike Air Max</h3>
                </div>
            </div>

            <div class="product">
                <img src="images/Screenshot 2025-03-24 124204.png" alt="Product 2">
                <div class="info">
                    <h3>Adidas Ultraboost</h3>
                </div>
            </div>

            <div class="product">
                <img src="images/Screenshot 2025-03-24 124204.png" alt="Product 3">
                <div class="info">
                    <h3>Puma RS-X</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="sub-container">
        <div class="chart">
            <canvas id="myChart" width="400" height="330"></canvas>
        </div>
    </div>



    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "verosports";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Fail Connect: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM users WHERE user_id=15";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo '<div class="admin">
                <img src="../../upload/' . $row['profile_image'] . '" alt="Profile Image">
                <p>Welcome Back ! '.$row['first_name'].'</p>
                <p>Email: '.$row['email'].'</p>
                <input type="button" name="" id=""><i class="fa-solid fa-right-from-bracket"></i></input>
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
                data: [60, 25, 15], 
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
</script>
    


</body>

</html>