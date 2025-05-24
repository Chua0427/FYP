<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods - VeroSports</title>
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
        
        .payment-container {
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
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        
        h2 {
            color: #3498db;
            margin-top: 25px;
            font-size: 1.3em;
            text-align: center;
        }
        
        p {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .payment-method {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #eee;
            width: 180px;
            transition: transform 0.3s ease;
        }
        
        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .payment-method img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
            object-fit: contain;
        }
        
        .note {
            font-style: italic;
            color: #666;
            margin-top: 20px;
            text-align: center;
        }
        
        .highlight {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .payment-container {
                padding: 20px;
                margin: 15px;
            }
            
            h1 {
                font-size: 1.6em;
            }
            
            .payment-methods {
                gap: 15px;
            }
            
            .payment-method {
                width: 140px;
                padding: 20px;
            }
            
            .payment-method img {
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="payment-container">
        <h1>PAYMENT METHODS</h1>
        
        <div class="highlight">
            <p>We accept payments through major credit cards. Please note that we reserve the right to amend prices on this website at any time.</p>
        </div>
        
        <h2>Accepted Payment Methods</h2>
        <div class="payment-methods">
            <div class="payment-method">
                <img src="../fl/image/visa.png" alt="Visa">
            </div>
            <div class="payment-method">
                <img src="../fl/image/mastercard.png" alt="Mastercard">
            </div>
        </div>
        
        <div class="highlight">
            <p> Products will be shipped after payment is processed.</p>
        </div>
        
        <div class="note">
            <p>Note: While we strive to maintain accurate pricing, errors may occasionally occur. In such cases, you may cancel your order before delivery.</p>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>