<?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penghantaran & Pengambilan - VeroSports</title>
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            line-height: 1.7;
            color: var(--text-color);
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
        }
        
        .delivery-container {
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
        
        .delivery-container::before {
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
            margin-bottom: 40px;
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
            color: var(--secondary-color);
            margin-top: 40px;
            font-size: 1.6em;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 12px;
            color: var(--primary-color);
        }
        
        p, li {
            margin-bottom: 18px;
            font-size: 1.05em;
            color: var(--text-color);
        }

        
        .highlight {
            background-color: var(--highlight-color);
            padding: 20px;
            border-left: 4px solid #ffd600;
            margin: 25px 0;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .highlight p {
            margin: 0;
            font-weight: 500;
        }
        
        .contact-box {
            background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border: 1px solid #d1e3f6;
            box-shadow: 0 3px 15px rgba(0,0,0,0.03);
        }
        
        .contact-box p {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .contact-box p i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .contact-box ul {
            margin-top: 15px;
        }
        
        .language-switcher {
            text-align: right;
            margin-bottom: 30px;
        }
        
        .language-switcher a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: 1px solid var(--primary-color);
        }
        
        .language-switcher a:hover {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .language-switcher a i {
            margin-right: 8px;
        }
        
        .delivery-icon {
            font-size: 1.2em;
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .policy-section {
            margin-bottom: 35px;
        }
        
        .policy-section:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .delivery-container {
                padding: 25px;
                margin: 15px;
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
            
            .contact-box {
                padding: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .delivery-container {
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
    
    <div class="delivery-container">
        <div class="language-switcher">
            <a href="AboutDeliveryEN.php"><i class="fas fa-language"></i>Lihat dalam Bahasa Inggeris</a>
        </div>
        
        <h1>Polisi Penghantaran & Pengambilan</h1>
        
        <div class="policy-section">
            <h2><i class="fas fa-truck delivery-icon"></i>Tempoh Penghantaran</h2>
            <p>Kami berusaha menghantar pesanan anda dalam tempoh yang ditunjukkan di laman web kami. Sila ambil perhatian:</p>
            <ul>
                <li>"Hari bekerja" bermaksud hari bekerja sahaja (tidak termasuk hujung minggu dan cuti umum)</li>
                <li>Semua tarikh penghantaran adalah anggaran sahaja</li>
                <li>Kami tidak bertanggungjawab jika pesanan dihantar melebihi tempoh yang dianggarkan</li>
                <li>Jika terdapat kelewatan, kami akan memaklumkan anda dengan anggaran baru</li>
            </ul>
            
            <div class="highlight">
                <p><i class="fas fa-exclamation-circle"></i> <strong>Penting:</strong> Jika item kehabisan stok, kami akan memaklumkan anda melalui emel.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-boxes delivery-icon"></i>Pesanan Pelbagai Produk</h2>
            <ul>
                <li>Pesanan dengan pelbagai produk mungkin dihantar dalam beberapa bahagian</li>
                <li>Kami berhak membahagikan penghantaran</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-shield-alt delivery-icon"></i>Pemilikan & Risiko</h2>
            <ul>
                <li>Pemilikan beralih kepada anda semasa penghantaran</li>
                <li>Produk menjadi risiko anda dari masa penghantaran</li>
                <li>Anda perlu menjaga barang yang dihantar dengan wajar</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-exclamation-triangle delivery-icon"></i>Penghantaran Salah</h2>
            <p>Jika anda menerima produk atau kuantiti yang salah akibat kesilapan kami:</p>
            
            <div class="contact-box">
                <p><i class="fas fa-headset"></i><strong>Sila hubungi kami segera:</strong></p>
                <ul>
                    <li><i class="fas fa-phone"></i> Telefon: [012-3456789]</li>
                    <li><i class="fas fa-envelope"></i> Emel: support@verosports        .com</li>
                </ul>
            </div>
            
            <ul>
                <li>Kami akan membayar balik kos penghantaran pulang untuk item yang salah</li>
                <li>Hubungi kami sebelum memulangkan sebarang item</li>
                <li>Jangan gunakan produk yang diterima secara salah</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-map-marker-alt delivery-icon"></i>Alamat Penghantaran</h2>
            <ul>
                <li>Pesanan dihantar ke alamat yang diberikan semasa checkout</li>
                <li>Sila pastikan alamat penghantaran anda sah dan tepat</li>
                <li>Kaedah penghantaran berbeza mengikut Rakan Penghantar dan jenis pesanan</li>
                <li>Kami memilih kaedah penghantaran yang paling sesuai</li>
            </ul>
            
            <div class="highlight">
                <p><i class="fas fa-info-circle"></i> <strong>Pengaturan Penghantaran Alternatif:</strong> Kami mungkin meninggalkan pesanan dengan jiran atau di tempat selamat di harta anda, bergantung pada Rakan Penghantar.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-question-circle delivery-icon"></i>Penghantaran Gagal</h2>
            <p>Jika pesanan anda tidak dihantar seperti yang dijangkakan:</p>
            <ul>
                <li>Maklumkan kepada kami dalam tempoh 7 hari selepas penghantaran gagal</li>
                <li>Hubungi pasukan khidmat pelanggan kami dengan segera</li>
            </ul>
        </div>
        
        <div class="policy-section">
            <h2><i class="fas fa-store delivery-icon"></i>Pilihan Pengambilan</h2>
            <p>Untuk pelanggan yang lebih suka mengambil pesanan mereka:</p>
            <ul>
                <li>Tersedia di lokasi terpilih sahaja</li>
                <li>Anda akan dimaklumkan apabila pesanan anda sedia untuk diambil</li>
                <li>Sila bawa pengesahan pesanan dan ID yang sah</li>
                <li>Pengambilan mesti dibuat dalam tempoh 7 hari selepas pemberitahuan</li>
            </ul>
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>