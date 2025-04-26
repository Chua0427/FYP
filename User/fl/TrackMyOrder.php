<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Order - VeroSports</title>
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
        
        .tracking-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
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
        }
        
        p {
            margin-bottom: 15px;
        }
        
        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        
        .note {
            font-style: italic;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .tracking-container {
                padding: 20px;
                margin: 15px;
            }
            
            h1 {
                font-size: 1.6em;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="tracking-container">
        <h1>Track My Order</h1>
        
        <div class="info-box">
            <p>You can track your order using the unique tracking reference number found in:</p>
            <ul>
                <li>Your order history</li>
                <li>The confirmation email we sent you</li>
            </ul>
        </div>
        
        <h2>Tracking Process</h2>
        <p>Once your order is processed, our system will automatically email you the tracking number. Please check your inbox, spam, or important mailbox for this email.</p>
        
        <p>You can also track your order directly through our delivery partner's website using the provided tracking number.</p>
        
        <h2>Delivery Information</h2>
        <p>Our delivery partners will require a signature to confirm you've received your items in good condition.</p>
        
        <p>If you're unavailable during the first delivery attempt:</p>
        <ul>
            <li>They may leave it with a neighbor</li>
            <li>Or in a safe place (hidden from sight and protected from weather)</li>
            <li>A contact card will be left at your door with delivery details</li>
        </ul>
        
        <h2>Delivery Attempts</h2>
        <p>Our delivery partners will attempt re-delivery up to three (3) times. If unsuccessful after the third attempt:</p>
        <ul>
            <li>The goods will be returned to us</li>
            <li>We will issue you a Credit Note</li>
        </ul>
        
        <p class="note">Note: We reserve the right to re-charge any delivery costs for re-delivery attempts.</p>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>