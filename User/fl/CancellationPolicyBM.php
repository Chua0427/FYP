<?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polisi Pembatalan - VeroSports</title>
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
            margin: 40px auto;
            padding: 40px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 10px;
            position: relative;
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
            margin-bottom: 30px;
            font-size: 2.2em;
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
            font-size: 1.5em;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
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
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .warning {
            background-color: #ffebee;
            padding: 20px;
            border-left: 4px solid var(--warning-color);
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
            margin-bottom: 25px;
        }
        
        .language-switcher a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 4px;
            border: 1px solid var(--primary-color);
            transition: all 0.2s ease;
        }
        
        .language-switcher a:hover {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .language-switcher a i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .policy-container {
                padding: 25px;
                margin: 20px;
            }
            
            h1 {
                font-size: 1.8em;
            }
            
            h1::after {
                width: 70px;
            }
        }
        
        @media (max-width: 480px) {
            .policy-container {
                padding: 20px;
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
            <a href="CancellationPolicyEN.php"><i class="fas fa-language"></i>View in English</a>
        </div>
        
        <h1>Polisi Pembatalan</h1>
        
        <div class="warning">
            <p><strong>Penting:</strong> Tiada pembatalan pesanan atau bayaran balik akan dilayan.</p>
        </div>
        
        <h2>Pilihan Penukaran</h2>
        <p>Sebagai ganti pembatalan, anda boleh memilih untuk menukar barang yang dipulangkan di bawah syarat-syarat berikut:</p>
        
        <div class="highlight">
            <ul>
                <li>Penukaran hanya untuk barang yang bernilai sama atau lebih tinggi</li>
                <li>Bayaran tambahan diperlukan untuk barang yang lebih tinggi nilainya</li>
                <li>Barang asal mestilah dalam keadaan sempurna</li>
                <li>Semua label dan pembungkusan asal mesti utuh</li>
                <li>Penukaran mesti dibuat dalam tempoh 7 hari selepas pembelian</li>
                <li>Resit asal mesti ditunjukkan</li>
            </ul>
        </div>
        
        <h2>Nota Penting</h2>
        <ul>
            <li>Tiada bayaran balik akan dikeluarkan untuk sebarang pesanan yang dibatalkan</li>
            <li>Barang promosi mungkin mempunyai polisi penukaran yang berbeza</li>
            <li>Barang jualan akhir tidak boleh ditukar</li>
            <li>Barang peribadi/tempahan tidak boleh ditukar</li>
        </ul>
        
        <p>Untuk maklumat lebih terperinci tentang proses penukaran kami, sila rujuk <a href="RPen.php" class="link"><b>Polisi Pulangan</b></a> kami.</p>
        
        <div class="warning">
            <p><strong>Nota:</strong> Polisi ini terpakai untuk semua pembelian yang dibuat melalui laman web kami, aplikasi mudah alih, dan kedai fizikal.</p>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>