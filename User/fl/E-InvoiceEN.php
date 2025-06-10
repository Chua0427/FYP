<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Invoice FAQ - VeroSports</title>
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
        
        .faq-container {
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
            font-size: 2.2em;
        }
        
        h2 {
            color: #3498db;
            margin-top: 40px;
            font-size: 1.5em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .faq-item {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .question {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
        }
        
        .question .q {
            color: #3498db;
            margin-right: 8px;
        }
        
        .answer {
            color: #555;
            padding-left: 20px;
        }
        
        .answer .a {
            color: #4CAF50;
            margin-right: 8px;
        }
        
        .highlight {
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
            .faq-container {
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
    
    <div class="faq-container">        
        <h1>E-Invoice Frequently Asked Questions</h1>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> What is e-invoicing?</div>
            <div class="answer"><span class="a">A:</span> E-invoicing is a digital transaction that makes it easier for customers to claim tax on the sport item they have purchased.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> Is e-invoicing safe?</div>
            <div class="answer"><span class="a">A:</span> Yes, because it is issued by a registered company, and companies will carefully check with the requester.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> Is e-invoicing only for physical purchases?</div>
            <div class="answer"><span class="a">A:</span> No, e-invoicing can be used for both physical purchases and online purchases.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> How long is the validity period?</div>
            <div class="answer"><span class="a">A:</span> The validity period is within the same month of purchase only.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> How do I get an e-invoice?</div>
            <div class="answer"><span class="a">A:</span> You can obtain an e-invoice at every point of your purchase.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> I have bought at Store A, can I get an e-invoice at Store C?</div>
            <div class="answer"><span class="a">A:</span> Sorry, it's not possible. E-Invoice can only be claimed at the store where you made the purchase with the same month.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> Will I get an e-invoice after making a purchase?</div>
            <div class="answer"><span class="a">A:</span> Yes, after making a purchase the e-Invoice will be auto send to your registered email..</div>
        </div>

        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> What do I need to prepare to get an e-invoice?</div>
            <div class="answer"><span class="a">A:</span> You do not need to prepare anything as the e-Invoice will be auto send to your registered email.</div>
        </div>
        
        <div class="faq-item">
            <div class="question"><span class="q">Q:</span> Where can I read about this e-invoicing?</div>
            <div class="answer"><span class="a">A:</span> You can visit <a href="https://www.hasil.gov.my/e-invoice/" target="_blank">https://www.hasil.gov.my/e-invoice/</a> for more information.</div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>