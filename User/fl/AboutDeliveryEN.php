<?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery & Collection - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #ff6b6b;
            --light-bg: #f8f9fa;
            --highlight-color: #fffde7;
            --border-color: #e0e0e0;
            --text-color: #333;
            --text-light: #777;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
        }
        
        .delivery-container {
            max-width: 1000px;
            margin-right: auto;
            margin-left: auto;
            margin-bottom: 130px;
            padding: 30px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            position: relative;
            top: 98px;
        }
        
        .delivery-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), #2980b9);
        }
        
        h1 {
            color: var(--secondary-color);
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.4em;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        h2 {
            color: var(--secondary-color);
            margin-top: 40px;
            font-size: 1.6em;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 12px;
            color: var(--primary-color);
        }
        
        p, li {
            margin-bottom: 18px;
            font-size: 1.05em;
            color: var(--text-color);
        }
        
        .highlight {
            background-color: var(--highlight-color);
            padding: 20px;
            border-left: 4px solid #ffd600;
            margin: 25px 0;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .highlight p {
            margin: 0;
            font-weight: 500;
        }
        
        .contact-box {
            background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border: 1px solid #d1e3f6;
            box-shadow: 0 3px 15px rgba(0,0,0,0.03);
        }
        
        .contact-box p {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .contact-box p i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .contact-box ul {
            margin-top: 15px;
        }
        
        .language-switcher {
            text-align: right;
            margin-bottom: 30px;
        }
        
        .language-switcher a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: 1px solid var(--primary-color);
        }
        
        .language-switcher a:hover {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .language-switcher a i {
            margin-right: 8px;
        }
        
        .delivery-icon {
            font-size: 1.2em;
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .policy-section {
            margin-bottom: 35px;
        }
        
        .policy-section:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .delivery-container {
                padding: 25px;
                margin: 15px;
                border-radius: 8px;
            }
            
            h1 {
                font-size: 1.8em;
                padding-bottom: 10px;
            }
            
            h1::after {
                width: 70px;
                height: 3px;
            }
            
            h2 {
                font-size: 1.4em;
            }
            
            .contact-box {
                padding: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .delivery-container {
                padding: 20px 15px;
            }
            
            h1 {
                font-size: 1.6em;
            }
            
            .language-switcher a {
                padding: 6px 12px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="delivery-container">
        <div class="language-switcher">
            <a href="AboutDeliveryBM.php"><i class="fas fa-language"></i>View in Bahasa Malaysia</a>
        </div>
        
        <h1>Delivery & Collection Policy</h1>
        
        <div class="policy-section">
            <h2><i class="fas fa-truck delivery-icon"></i>Delivery Timescales</h2>
            <p>We aim to deliver your orders within the indicative timescales shown on our website. Please note:</p>
            <ul>
                <li>A "working day" means any day excluding weekends and public holidays</li>
                <li>All delivery dates are estimates only</li>
                <li>We are not responsible if orders are delivered outside estimated times</li>
                <li>If delayed, we will inform you with an amended delivery estimate</li>
            </ul>
            
            <div class="highlight">
                <p><i class="fas fa-exclamation-circle"></i> <strong>Important:</strong> If items are out of stock, we will notify you by email.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-boxes delivery-icon"></i>Multiple Product Orders</h2>
            <ul>
                <li>Orders with multiple products may be delivered in multiple consignments</li>
                <li>We reserve the right to split deliveries</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-shield-alt delivery-icon"></i>Ownership & Risk</h2>
            <ul>
                <li>Ownership transfers to you upon delivery</li>
                <li>Products are at your risk from time of delivery</li>
                <li>You should take reasonable care of delivered items</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-exclamation-triangle delivery-icon"></i>Incorrect Deliveries</h2>
            <p>If you receive incorrect products or quantities due to our error:</p>
            
            <div class="contact-box">
                <p><i class="fas fa-headset"></i><strong>Please contact us immediately:</strong></p>
                <ul>
                    <li><i class="fas fa-phone"></i> Phone/WhatsApp: [Your Number]</li>
                    <li><i class="fas fa-envelope"></i> Email: verosports11@gmail.com</li>
                </ul>
            </div>
            
            <ul>
                <li>We will reimburse return delivery costs for incorrect items</li>
                <li>Contact us before returning any items</li>
                <li>Do not use products received in error</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-map-marker-alt delivery-icon"></i>Delivery Address</h2>
            <ul>
                <li>Orders are delivered to the address provided during checkout</li>
                <li>Please ensure your delivery address is valid and accurate</li>
                <li>Delivery methods vary by Delivery Partner and order nature</li>
                <li>We select the most appropriate delivery method</li>
            </ul>
            
            <div class="highlight">
                <p><i class="fas fa-info-circle"></i> <strong>Alternative Delivery Arrangements:</strong> We may leave orders with a neighbor or in a safe place at your property, depending on the Delivery Partner.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-question-circle delivery-icon"></i>Non-Delivery</h2>
            <p>If your order is not delivered as expected:</p>
            <ul>
                <li>Notify us of non-delivery within 7 days of the failed delivery</li>
                <li>Contact our customer service team immediately</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-store delivery-icon"></i>Collection Option</h2>
            <p>For customers who prefer to collect their orders:</p>
            <ul>
                <li>Available at selected locations only</li>
                <li>You will be notified when your order is ready for collection</li>
                <li>Please bring your order confirmation and valid ID</li>
                <li>Collection must be made within 7 days of notification</li>
            </ul>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>