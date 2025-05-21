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
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        
        .tracking-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 35px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        h1 {
            color: #2a4365;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2rem;
            position: relative;
        }
        
        h1:after {
            content: "";
            display: block;
            width: 60px;
            height: 3px;
            background: #3182ce;
            margin: 10px auto 0;
        }
        
        h2 {
            color: #3182ce;
            margin: 25px 0 15px;
            font-size: 1.3rem;
        }
        
        p {
            color: #4a5568;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        
        .info-box {
            background: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 18px;
            border-radius: 0 6px 6px 0;
            margin: 20px 0;
        }
        
        ul {
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 8px;
            color: #4a5568;
        }
        
        .note {
            background: #fffaf0;
            padding: 12px;
            border-left: 4px solid #dd6b20;
            font-style: italic;
            color: #718096;
        }
        
        @media (max-width: 768px) {
            .tracking-container {
                padding: 25px 20px;
                margin: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header remains COMPLETELY UNCHANGED -->
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
    
    <!-- Footer remains COMPLETELY UNCHANGED -->
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>