<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Locations - VeroSports</title>
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
        
        .locations-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em;
        }
        
        .store-locator {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .search-box {
            flex: 1;
            min-width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            margin-bottom: 15px;
        }
        
        .store-list {
            flex: 1;
            min-width: 300px;
            max-height: 500px;
            overflow-y: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .store-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .store-item:hover {
            background: #f9f9f9;
        }
        
        .store-item h3 {
            color: #3498db;
            margin-bottom: 5px;
        }
        
        .store-item p {
            margin: 5px 0;
            color: #666;
        }
        
        .store-item .hours {
            font-style: italic;
            color: #888;
        }
        
        .map-container {
            flex: 2;
            min-height: 400px;
            background: #eee;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        #store-map {
            width: 100%;
            height: 100%;
        }
        
        .all-stores {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .store-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .store-card h3 {
            color: #3498db;
            margin-top: 0;
        }
        
        .directions-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .store-locator {
                flex-direction: column;
            }
            
            .map-container {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="locations-container">
        <h1>Our Store Locations</h1>
        
        <div class="store-locator">
            <div class="search-box">
                <input type="text" id="location-search" placeholder="Search by city or postcode">
                <div class="store-list" id="store-list">
                    <!-- Stores will be loaded here by JavaScript -->
                    <div class="store-item">
                        <h3>VeroSports Kuala Lumpur</h3>
                        <p>Lot 123, Jalan Bukit Bintang</p>
                        <p>Kuala Lumpur 55100</p>
                        <p>Phone: 03-1234 5678</p>
                        <p class="hours">Hours: 10AM - 10PM Daily</p>
                    </div>
                    <div class="store-item">
                        <h3>VeroSports Petaling Jaya</h3>
                        <p>Level 2, Paradigm Mall</p>
                        <p>Petaling Jaya 47800</p>
                        <p>Phone: 03-8765 4321</p>
                        <p class="hours">Hours: 10AM - 10PM Daily</p>
                    </div>
                    <!-- More stores would be listed here -->
                </div>
            </div>
            
            <div class="map-container">
                <div id="store-map">
                    <!-- Google Maps will be loaded here -->
                    <img src="https://maps.googleapis.com/maps/api/staticmap?center=Kuala+Lumpur&zoom=11&size=800x400&maptype=roadmap&markers=color:red%7C3.1390,101.6869&markers=color:red%7C3.1076,101.6067&key=YOUR_API_KEY" alt="VeroSports Store Locations" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>
        </div>
        
        <h2 style="text-align: center; margin-bottom: 20px;">All VeroSports Stores</h2>
        
        <div class="all-stores">
            <div class="store-card">
                <h3>VeroSports Kuala Lumpur</h3>
                <p>Lot 123, Jalan Bukit Bintang</p>
                <p>Kuala Lumpur 55100</p>
                <p>Phone: 03-1234 5678</p>
                <p>Email: kl@verosports.com</p>
                <p class="hours">Hours: 10AM - 10PM Daily</p>
                <a href="https://maps.google.com?q=VeroSports+Kuala+Lumpur" class="directions-btn" target="_blank">Get Directions</a>
            </div>
            
            <div class="store-card">
                <h3>VeroSports Petaling Jaya</h3>
                <p>Level 2, Paradigm Mall</p>
                <p>Petaling Jaya 47800</p>
                <p>Phone: 03-8765 4321</p>
                <p>Email: pj@verosports.com</p>
                <p class="hours">Hours: 10AM - 10PM Daily</p>
                <a href="https://maps.google.com?q=VeroSports+Petaling+Jaya" class="directions-btn" target="_blank">Get Directions</a>
            </div>
            
            <div class="store-card">
                <h3>VeroSports Penang</h3>
                <p>G-25, Gurney Plaza</p>
                <p>George Town 10250</p>
                <p>Phone: 04-9876 5432</p>
                <p>Email: penang@verosports.com</p>
                <p class="hours">Hours: 10AM - 10PM Daily</p>
                <a href="https://maps.google.com?q=VeroSports+Penang" class="directions-btn" target="_blank">Get Directions</a>
            </div>
            
            <!-- More store cards would be listed here -->
        </div>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

    <script>
        // This would be replaced with actual JavaScript to:
        // 1. Load store data from a database or JSON file
        // 2. Implement search functionality
        // 3. Initialize and manage an interactive Google Map
        // 4. Handle store selection and map markers
        
        // Sample implementation would include:
        /*
        const stores = [
            {
                name: "VeroSports Kuala Lumpur",
                address: "Lot 123, Jalan Bukit Bintang",
                city: "Kuala Lumpur 55100",
                phone: "03-1234 5678",
                hours: "10AM - 10PM Daily",
                lat: 3.1390,
                lng: 101.6869
            },
            // More store objects...
        ];
        
        function initMap() {
            // Initialize Google Map with markers
        }
        
        function filterStores(searchTerm) {
            // Filter stores based on search input
        }
        */
    </script>
    <!-- Google Maps API would be loaded here -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script> -->
</body>
</html>