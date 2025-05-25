<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Location - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        .location-container {
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
            text-align: center;
            color: #2c3e50;
            font-size: 2.4em;
            margin-bottom: 30px;
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
            background: #3498db;
            border-radius: 2px;
        }

        .map {
            width: 100%;
            height: 450px;
            border: none;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .info {
            font-size: 1.05em;
            line-height: 1.7;
        }

        .info p i {
            color: #3498db;
            margin-right: 10px;
        }

        .info p {
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .location-container {
                padding: 25px;
                margin: 15px;
            }

            h1 {
                font-size: 1.8em;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.6em;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>

    <div class="location-container">
        <h1>Our location</h1>
        <iframe class="map"
            src=https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15946.93316457354!2d102.27980360203357!3d2.253298344234474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d1e56b9710cf4b%3A0x66b6b12b75469278!2z6ams5YWt55Sy5aSa5aqS5L2T5aSn5a2m!5e0!3m2!1szh-CN!2smy!4v1746677721633!5m2!1szh-CN!2smy"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>

        <div class="info">
            <p><i class="fas fa-map-marker-alt"></i>Address: No. 123, Jalan Utama, 50000 Kuala Lumpur, Malaysia</p>
            <p><i class="fas fa-phone"></i>Contact Number: 012-3456789</p>
            <p><i class="fas fa-clock"></i>Working Hour: Monday - Saturday, 10:00 a.m. - 7:00 p.m.</p>
            <p><i class="fas fa-envelope"></i>Email: verosports11@gmail.com</p>
        </div>
    </div>

    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>
