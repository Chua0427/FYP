<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mission & Vision - VEROSPORTS</title>
  <link rel="stylesheet" href="../Header_and_Footer/header.css"/>
  <link rel="stylesheet" href="../Header_and_Footer/footer.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

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
      background: linear-gradient(to right, #e0f7fa, #ffffff);
      margin: 0;
      padding: 0;
      color: var(--text-color);
    }

    .mission-container {
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

    .mission-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 6px;
      height: 100%;
      background: linear-gradient(to bottom, var(--primary-color), #2980b9);
    }

    h1 {
      text-align: center;
      font-size: 2.5em;
      color: var(--secondary-color);
      margin-bottom: 40px;
      position: relative;
      padding-bottom: 15px;
    }

    h1::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--primary-color);
      border-radius: 4px;
    }

    h2 {
      font-size: 1.7em;
      margin-top: 50px;
      color: var(--secondary-color);
      border-bottom: 2px solid var(--border-color);
      padding-bottom: 10px;
      display: flex;
      align-items: center;
    }

    h2 i {
      color: var(--primary-color);
      margin-right: 12px;
      font-size: 1.3em;
    }

    .vision-mission-card {
      background: #fefefe;
      border-left: 6px solid var(--primary-color);
      padding: 30px;
      margin: 25px 0;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }

    .vision-mission-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .vision-mission-card h3 {
      font-size: 1.5em;
      color: var(--primary-color);
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }

    .vision-mission-card h3 i {
      margin-right: 10px;
      font-size: 1.4em;
    }

    .vision-mission-card p {
      font-size: 1.05em;
      color: var(--text-color);
    }

    @media (max-width: 768px) {
      .mission-container {
        padding: 25px 20px;
        margin: 30px 15px;
      }

      h1 {
        font-size: 2em;
      }

      h2 {
        font-size: 1.5em;
      }

      .vision-mission-card {
        padding: 22px;
      }
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.7em;
      }

      h2 {
        font-size: 1.3em;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

  <div class="mission-container">
    <h1>Mission & Vision</h1>

    <div class="vision-mission-card">
      <h3><i class="fas fa-bullseye"></i> Our Vision</h3>
      <p>At VEROSPORTS, our vision is to revolutionize the sports industry by offering top-tier products that blend performance with style. We aspire to be the leading sports brand that inspires athletes at all levels to push their limits while looking their best.</p>
    </div>

    <div class="vision-mission-card">
      <h3><i class="fas fa-flag"></i> Our Mission</h3>
      <p>Our mission is to provide high-quality, innovative sports gear that enhances performance while maintaining exceptional comfort and durability. We commit to sustainable practices, customer satisfaction, and continuous innovation in sports technology.</p>
    </div>

    <h2><i class="fas fa-star"></i> Core Values</h2>

    <div class="vision-mission-card">
      <h3><i class="fas fa-medal"></i> Excellence</h3>
      <p>We strive for excellence in every product we create, ensuring premium quality and performance that athletes can rely on.</p>
    </div>

    <div class="vision-mission-card">
      <h3><i class="fas fa-heart"></i> Passion</h3>
      <p>Our team is driven by a shared passion for sports and a commitment to helping athletes achieve their personal best.</p>
    </div>

    <div class="vision-mission-card">
      <h3><i class="fas fa-leaf"></i> Sustainability</h3>
      <p>We're committed to environmentally responsible practices throughout our manufacturing and distribution processes.</p>
    </div>

    <div class="vision-mission-card">
      <h3><i class="fas fa-users"></i> Community</h3>
      <p>We believe in building strong relationships with our customers and supporting sports communities worldwide.</p>
    </div>
  </div>

  <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>
