<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penghantaran & Pengambilan - VeroSports</title>
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
        
        .delivery-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em;
        }
        
        h2 {
            color: #3498db;
            margin-top: 30px;
            font-size: 1.5em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        p, li {
            margin-bottom: 15px;
            font-size: 1em;
        }
        
        ul {
            padding-left: 20px;
        }
        
        .highlight {
            background-color: #fffde7;
            padding: 15px;
            border-left: 4px solid #ffd600;
            margin: 20px 0;
        }
        
        .contact-box {
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
            .delivery-container {
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
    
    <div class="delivery-container">
        <div class="language-switcher">
            <a href="AboutDeliveryEN.php">View in English</a>
        </div>
        
        <h1>Polisi Penghantaran & Pengambilan</h1>
        
        <h2>Tempoh Penghantaran</h2>
        <p>Kami berusaha menghantar pesanan anda dalam tempoh yang ditunjukkan di laman web kami. Sila ambil perhatian:</p>
        <ul>
            <li>"Hari bekerja" bermaksud hari bekerja sahaja (tidak termasuk hujung minggu dan cuti umum)</li>
            <li>Semua tarikh penghantaran adalah anggaran sahaja</li>
            <li>Kami tidak bertanggungjawab jika pesanan dihantar melebihi tempoh yang dianggarkan</li>
            <li>Jika terdapat kelewatan, kami akan memaklumkan anda dengan anggaran baru</li>
        </ul>
        
        <div class="highlight">
            <p><strong>Nota:</strong> Jika item kehabisan stok, kami akan memaklumkan anda melalui emel.</p>
        </div>
        
        <h2>Pesanan Pelbagai Produk</h2>
        <ul>
            <li>Pesanan dengan pelbagai produk mungkin dihantar dalam beberapa bahagian</li>
            <li>Kami berhak membahagikan penghantaran</li>
        </ul>
        
        <h2>Pemilikan & Risiko</h2>
        <ul>
            <li>Pemilikan beralih kepada anda semasa penghantaran</li>
            <li>Produk menjadi risiko anda dari masa penghantaran</li>
            <li>Anda perlu menjaga barang yang dihantar dengan wajar</li>
        </ul>
        
        <h2>Penghantaran Salah</h2>
        <p>Jika anda menerima produk atau kuantiti yang salah akibat kesilapan kami:</p>
        
        <div class="contact-box">
            <p><strong>Sila hubungi kami segera:</strong></p>
            <ul>
                <li>Telefon/WhatsApp: </li>
                <li>Emel: verosports11@gmail.com</li>
            </ul>
        </div>
        
        <ul>
            <li>Kami akan membayar balik kos penghantaran pulang untuk item yang salah</li>
            <li>Hubungi kami sebelum memulangkan sebarang item</li>
            <li>Jangan gunakan produk yang diterima secara salah</li>
        </ul>
        
        <h2>Alamat Penghantaran</h2>
        <ul>
            <li>Pesanan dihantar ke alamat yang diberikan semasa checkout</li>
            <li>Sila pastikan alamat penghantaran anda sah dan tepat</li>
            <li>Kaedah penghantaran berbeza mengikut Rakan Penghantar dan jenis pesanan</li>
            <li>Kami memilih kaedah penghantaran yang paling sesuai</li>
        </ul>
        
        <div class="highlight">
            <p><strong>Pengaturan Penghantaran Alternatif:</strong> Kami mungkin meninggalkan pesanan dengan jiran atau di tempat selamat di harta anda, bergantung pada Rakan Penghantar.</p>
        </div>
        
        <h2>Penghantaran Gagal</h2>
        <p>Jika pesanan anda tidak dihantar seperti yang dijangkakan:</p>
        <ul>
            <li>Maklumkan kepada kami dalam tempoh 7 hari selepas penghantaran gagal</li>
            <li>Hubungi pasukan khidmat pelanggan kami dengan segera</li>
        </ul>
        
        <h2>Pilihan Pengambilan</h2>
        <p>Untuk pelanggan yang lebih suka mengambil pesanan mereka:</p>
        <ul>
            <li>Tersedia di lokasi terpilih sahaja</li>
            <li>Anda akan dimaklumkan apabila pesanan anda sedia untuk diambil</li>
            <li>Sila bawa pengesahan pesanan dan ID yang sah</li>
            <li>Pengambilan mesti dibuat dalam tempoh 7 hari selepas pemberitahuan</li>
        </ul>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>