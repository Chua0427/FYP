<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="delivery.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.html'; ?>

    <h1>Tracking Delivery</h1><p></p>


        <div class="delivery-status">
            <div class="step" id="preparing">
                <div class="circle"><i class="fa-solid fa-store"></i></div>
                <p>Preparing Your Order</p>
            </div>
            <div class="step" id="packing">
                <div class="circle"><i class="fa-solid fa-box"></i></div>
                <p>Packing Your Order</p>
            </div>
            <div class="step" id="assign">
                <div class="circle"><i class="fa-solid fa-truck"></i></div>
                <p>Assigning Our Driver</p>
            </div>
            <div class="step" id="shipping">
                <div class="circle"><i class="fa-solid fa-truck-fast"></i></div>
                <p>Order Is On The Way</p>
            </div>
            <div class="step" id="delivered">
                <div class="circle"><i class="fa-solid fa-location-dot"></i></div>
                <p>Delivered</p>
            </div>
        </div>

        <div class="invoice">
            <h2>Order</h2>
            <p style="margin-top: 20px;"><strong>Order ID:</strong> 12345</p>
            <p><strong>Date:</strong> 13/2/2025</p>
            <p><strong>Customer:</strong> Elvis</p>
            <p><strong>Shipping Address:</strong> No 26, Jalan Oren 3, Taman Cantik , 85000 Segamat, Johor</p>
            
            <h3 >Order Items</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>Nike</td>
                    <td>2</td>
                    <td style="color: red;">RM 100</td>
                </tr>
            </table>

            <p style="margin-bottom: 20px;"><strong>Total Price:</strong> RM 200</p>
            <hr>
            <p style="margin-top:10px; margin-bottom: 10px;">Note: </p>
            <p>If you have any problem about delivery, please email to our customer service (Email:support@verosports.com)</p>
        </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.html'; ?>
    <script src="delivery.js"></script>
    
</body>

</html>