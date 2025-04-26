<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About VeroSports - Your Ultimate Sports Destination</title>
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
        
        .about-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../images/about/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        
        h1 {
            font-size: 2.8em;
            margin-bottom: 20px;
        }
        
        .hero-section p {
            font-size: 1.2em;
            max-width: 800px;
            margin: 0 auto 30px;
        }
        
        .section {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.8em;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: #3498db;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .value-card {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .value-card i {
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .team-section {
            text-align: center;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .team-member {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #f5f5f5;
        }
        
        @media (max-width: 768px) {
            .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .hero-section {
                padding: 60px 20px;
            }
            
            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="about-container">
        <div class="hero-section">
            <h1>Welcome to VeroSports</h1>
            <p>Your premier destination for quality sports equipment and athletic wear since 2010</p>
        </div>
        
        <div class="section">
            <h2>Our Story</h2>
            <p>Founded in 2025, VEROSPORTS was born out of a passion for sports and a commitment to innovation. What started as a humble storefront has grown into Malaysia's leading sports retailer, with 15 locations nationwide and a thriving online store.</p>
            <p>Our journey has been fueled by our love for sports and commitment to providing only the highest quality equipment from the world's most trusted brands. Whether you're a professional athlete or just starting your fitness journey, we're here to support you every step of the way.</p>
        </div>
        
        <div class="section">
            <h2>Mission & Vision</h2>
            <div class="mission-vision">
                <div>
                    <h3>Our Mission</h3>
                    <p>To inspire and equip athletes of all levels with premium sports gear that enhances performance, durability, and comfort while providing exceptional customer service and expert advice.</p>
                </div>
                <div>
                    <h3>Our Vision</h3>
                    <p>To become Malaysia's most trusted sports retailer by continuously innovating our product offerings and creating a community where sports enthusiasts can connect, learn, and grow together.</p>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-medal"></i>
                    <h3>Quality First</h3>
                    <p>We carefully select every product in our inventory to ensure it meets our high standards for performance and durability.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>Customer Focus</h3>
                    <p>Your satisfaction is our priority. Our knowledgeable staff are always ready to help you find the perfect gear.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-running"></i>
                    <h3>Passion for Sports</h3>
                    <p>We're not just sellers - we're athletes too, and we understand what you need to perform at your best.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-globe-asia"></i>
                    <h3>Community Building</h3>
                    <p>We sponsor local teams and events because we believe in growing Malaysia's sports culture.</p>
                </div>
            </div>
        </div>
        
        <div class="section team-section">
            <h2>Meet Our Team</h2>
            <p>Behind VeroSports is a team of passionate sports enthusiasts and retail experts dedicated to serving you.</p>
            <div class="team-grid">
                <div class="team-member">
                    <img src="../fl/image/chiann.png" alt="Chua Chi Ann">
                    <h3>Chua Chi Ann</h3>
                    <p>Founder & CEO</p>
                    <p>Former national badminton player</p>
                </div>
                <div class="team-member">
                    <img src="../fl/image/soh.png" alt="Soh Xi Jie">
                    <h3>Soh Xi Jie</h3>
                    <p>Head of Retail</p>
                    <p>Fitness trainer and marathon runner</p>
                </div>
                <div class="team-member">
                    <img src="../fl/image/elvis.png" alt="Elvis Tan Kai Wen">
                    <h3>Elvis Tan Kai Wen</h3>
                    <p>Product Specialist</p>
                    <p>Football coach and equipment expert</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>