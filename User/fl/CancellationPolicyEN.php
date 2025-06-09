<?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellation Policy - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --warning-color: #f44336;
            --highlight-color: #ff9800;
            --light-bg: #f5f5f5;
            --text-color: #333;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
        }
        
        .policy-container {
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
        
        .policy-container::before {
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
            margin-bottom: 35px;
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
            color: var(--primary-color);
            margin-top: 35px;
            font-size: 1.6em;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 12px;
            color: var(--primary-color);
        }
        
        p, li {
            margin-bottom: 15px;
            font-size: 1.05em;
        }
        
        .highlight {
            background-color: #fff3e0;
            padding: 20px;
            border-left: 4px solid var(--highlight-color);
            margin: 25px 0;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .warning {
            background-color: #ffebee;
            padding: 20px;
            border-left: 4px solid var(--warning-color);
            margin: 25px 0;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .warning p, .highlight p {
            margin: 0;
            font-weight: 500;
        }
        
        
        .link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .link:hover {
            text-decoration: underline;
            color: #2980b9;
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
            .policy-container {
                padding: 25px;
                margin: 20px;
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
            .policy-container {
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
    <div class="policy-container">
        <div class="language-switcher">
            <a href="CancellationPolicyBM.php">View in Bahasa Malaysia</a>
        </div>
        
        <h1>Cancellation Policy</h1>
        
        <div class="warning">
            <p><i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> No cancellation of order or refund will be entertained.</p>
        </div>
        
        <h2><i class="fas fa-exchange-alt"></i>Exchange Option</h2>
        <p>Instead of cancellation, you may choose to exchange returned items under the following conditions:</p>
        
        <div class="highlight">
            <ul>
                <li>Exchange only for items of equal or higher value</li>
                <li>Additional payment required for higher value items</li>
                <li>Original merchandise must be in perfect condition</li>
                <li>All original tags and packaging must be intact</li>
                <li>Exchange must be made within 7 days of purchase</li>
                <li>Original receipt must be presented</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-info-circle"></i>Important Notes</h2>
        <ul>
            <li>No refunds will be issued for any cancelled orders</li>
            <li>Promotional items may have different exchange policies</li>
            <li>Final sale items cannot be exchanged</li>
            <li>Personalized/customized items cannot be exchanged</li>
        </ul>
        
        <p>For more detailed information about our exchange process, please refer to our <a href="RPen.php" class="link"><b>Return Policy</b></a>.</p>
        
        <div class="warning">
            <p><i class="fas fa-exclamation-circle"></i> <strong>Note:</strong> This policy applies to all purchases made through our website, mobile app, and physical stores.</p>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>