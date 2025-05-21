<?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - VEROSPORTS</title>
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
            line-height: 1.7;
            color: var(--text-color);
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
        }
        
        .terms-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }
        
        .terms-container::before {
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
        
        .policy-section {
            margin-bottom: 35px;
        }
        
        .policy-section:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .terms-container {
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
        }
        
        @media (max-width: 480px) {
            .terms-container {
                padding: 20px 15px;
            }
            
            h1 {
                font-size: 1.6em;
            }
        }
    </style>
</head>
<body>
    
    <div class="terms-container">
        <h1>Terms & Conditions</h1>
        
        <div class="policy-section">
            <h2><i class="fas fa-file-contract"></i> General Terms</h2>
            <p>By accessing and using the VEROSPORTS website, you accept and agree to be bound by these Terms and Conditions.</p>
            <ul>
                <li>All products are subject to availability</li>
                <li>We reserve the right to refuse service to anyone for any reason at any time</li>
                <li>Prices are subject to change without notice</li>
                <li>We are not responsible for content posted by users on our platform</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-shopping-cart"></i> Ordering & Payments</h2>
            <ul>
                <li>All orders are subject to acceptance and availability</li>
                <li>We accept various payment methods including credit cards and online banking</li>
                <li>You confirm that the payment details you provide are correct</li>
                <li>We reserve the right to cancel any order prior to delivery</li>
            </ul>
            
            <div class="highlight">
                <p><i class="fas fa-exclamation-circle"></i> <strong>Important:</strong> Your order is not confirmed until you receive an order confirmation email from us.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-truck"></i> Delivery Policy</h2>
            <ul>
                <li>Delivery times are estimates only</li>
                <li>We are not liable for any delays caused by third-party delivery services</li>
                <li>Risk of loss passes to you upon delivery</li>
                <li>You are responsible for providing accurate delivery information</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-undo"></i> Returns & Refunds</h2>
            <ul>
                <li>Items must be returned within 14 days of receipt</li>
                <li>Products must be unused and in original packaging</li>
                <li>Refunds will be processed within 7 business days</li>
                <li>Shipping costs for returns are the customer's responsibility</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-lock"></i> Privacy & Data Protection</h2>
            <ul>
                <li>We collect personal information to process your orders</li>
                <li>Your data will not be shared with third parties without your consent</li>
                <li>We implement security measures to protect your information</li>
                <li>You have the right to access and correct your personal data</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-gavel"></i> Intellectual Property</h2>
            <ul>
                <li>All content on this website is our property</li>
                <li>Unauthorized use is strictly prohibited</li>
                <li>The VEROSPORTS name and logo are registered trademarks</li>
                <li>You may not use our branding without written permission</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-ban"></i> Prohibited Uses</h2>
            <ul>
                <li>You may not use our products for any illegal purpose</li>
                <li>You may not reproduce, duplicate, or resell our products</li>
                <li>You may not use automated systems to access our website</li>
                <li>You may not transmit any viruses or malicious code</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-info-circle"></i> Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Your continued use of the website constitutes acceptance of the modified terms.</p>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>