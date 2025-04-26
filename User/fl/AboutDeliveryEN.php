<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery & Collection - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .delivery-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em;
        }
        
        h2 {
            color: #3498db;
            margin-top: 30px;
            font-size: 1.5em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        p, li {
            margin-bottom: 15px;
            font-size: 1em;
        }
        
        ul {
            padding-left: 20px;
        }
        
        .highlight {
            background-color: #fffde7;
            padding: 15px;
            border-left: 4px solid #ffd600;
            margin: 20px 0;
        }
        
        .contact-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .language-switcher {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .language-switcher a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        
        .language-switcher a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .delivery-container {
                padding: 20px;
                margin: 10px;
            }
            
            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="delivery-container">
        <div class="language-switcher">
            <a href="AboutDeliveryBM.php">View in Bahasa Malaysia</a>
        </div>
        
        <h1>Delivery & Collection Policy</h1>
        
        <h2>Delivery Timescales</h2>
        <p>We aim to deliver your orders within the indicative timescales shown on our website. Please note:</p>
        <ul>
            <li>A "working day" means any day excluding weekends and public holidays</li>
            <li>All delivery dates are estimates only</li>
            <li>We are not responsible if orders are delivered outside estimated times</li>
            <li>If delayed, we will inform you with an amended delivery estimate</li>
        </ul>
        
        <div class="highlight">
            <p><strong>Note:</strong> If items are out of stock, we will notify you by email.</p>
        </div>
        
        <h2>Multiple Product Orders</h2>
        <ul>
            <li>Orders with multiple products may be delivered in multiple consignments</li>
            <li>We reserve the right to split deliveries</li>
        </ul>
        
        <h2>Ownership & Risk</h2>
        <ul>
            <li>Ownership transfers to you upon delivery</li>
            <li>Products are at your risk from time of delivery</li>
            <li>You should take reasonable care of delivered items</li>
        </ul>
        
        <h2>Incorrect Deliveries</h2>
        <p>If you receive incorrect products or quantities due to our error:</p>
        
        <div class="contact-box">
            <p><strong>Please contact us immediately:</strong></p>
            <ul>
                <li>Phone/WhatsApp: </li>
                <li>Email: verosports11@gmail.com</li>
            </ul>
        </div>
        
        <ul>
            <li>We will reimburse return delivery costs for incorrect items</li>
            <li>Contact us before returning any items</li>
            <li>Do not use products received in error</li>
        </ul>
        
        <h2>Delivery Address</h2>
        <ul>
            <li>Orders are delivered to the address provided during checkout</li>
            <li>Please ensure your delivery address is valid and accurate</li>
            <li>Delivery methods vary by Delivery Partner and order nature</li>
            <li>We select the most appropriate delivery method</li>
        </ul>
        
        <div class="highlight">
            <p><strong>Alternative Delivery Arrangements:</strong> We may leave orders with a neighbor or in a safe place at your property, depending on the Delivery Partner.</p>
        </div>
        
        <h2>Non-Delivery</h2>
        <p>If your order is not delivered as expected:</p>
        <ul>
            <li>Notify us of non-delivery within 7 days of the failed delivery</li>
            <li>Contact our customer service team immediately</li>
        </ul>
        
        <h2>Collection Option</h2>
        <p>For customers who prefer to collect their orders:</p>
        <ul>
            <li>Available at selected locations only</li>
            <li>You will be notified when your order is ready for collection</li>
            <li>Please bring your order confirmation and valid ID</li>
            <li>Collection must be made within 7 days of notification</li>
        </ul>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>