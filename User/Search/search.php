<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    include __DIR__ . '/../Header_and_Footer/header.php'; 
    ?>

    <div class="search-container">
        <div class="search-box">
            <form id="searchForm">
                <input type="text" name="query" id="searchInput" placeholder="Enter Key Word...">
                <button type="submit" id="searchButton">Search</button>
            </form> 
        </div>

        <div id="results" class="search-results">
        <div class="product-wrapper">
            <div class="product-container"></div>
        </div>
        </div>
    </div>

    
    <script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const form = document.getElementById('searchForm');
        const formData = new FormData(form);

        fetch('search_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('results').innerHTML = html;
        }); 
    });
</script>

</body>

</html>
