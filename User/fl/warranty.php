<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Warranty - VeroSports</title>
  <link rel="stylesheet" href="../Header_and_Footer/header.css">
  <link rel="stylesheet" href="../Header_and_Footer/footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 0;
      color: #2c3e50;
    }

    .warranty-wrapper {
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

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 2.6em;
      text-align: center;
      color: #3498db;
      margin-bottom: 30px;
      border-bottom: 3px solid #3498db;
      display: inline-block;
      padding-bottom: 10px;
    }

    h2 {
      color: #34495e;
      font-size: 1.6em;
      margin-top: 30px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    h2 i {
      color: #3498db;
    }

    p {
      line-height: 1.8;
      margin: 15px 0;
    }

    a {
      color: #8e44ad;
      font-weight: 600;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .warranty-wrapper {
        margin: 20px;
        padding: 20px;
      }

      h1 {
        font-size: 2em;
      }

      h2 {
        font-size: 1.3em;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

  <div class="warranty-wrapper">
    <h1>Product Warranty</h1>

    <p>We believe in the quality of every product we sell. Our warranty ensures your peace of mind and continued satisfaction with your purchase at VeroSports.</p>

    <h2><i class="fas fa-shield-alt"></i> Warranty Coverage</h2>
    <p>Your purchase is covered for:</p>
    <ul>
      <li>Manufacturer defects in materials and workmanship</li>
      <li>Failures under normal and recommended use</li>
    </ul>

    <h2><i class="fas fa-clock"></i> Warranty Duration</h2>
    <ul>
      <li><strong>Footwear & Apparel:</strong> 6 months from purchase</li>
      <li><strong>Sports Equipment:</strong> 12 months</li>
      <li><strong>Electronics & Devices:</strong> 12–24 months (see product manual)</li>
    </ul>

    <h2><i class="fas fa-ban"></i> Not Covered</h2>
    <ul>
      <li>Damage due to misuse or accidental drops</li>
      <li>Wear and tear from extended usage (scratches, fading, etc.)</li>
      <li>Products without proof of purchase</li>
    </ul>

    <h2><i class="fas fa-headset"></i> How to Claim</h2>
    <p>To initiate a claim:</p>
    <ul>
      <li>Visit your purchase store or</li>
      <li>Email us at <a href="mailto:support@verosports.com">support@verosports.com</a> with your receipt and product issue description</li>
    </ul>

    <p>We are committed to resolving claims swiftly and professionally so you can get back to performing your best.</p>
  </div>

  <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>
    