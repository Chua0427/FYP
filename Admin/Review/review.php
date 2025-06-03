<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin (user_type = 2 or user_type = 3)
    if ($_SESSION['user_type'] != 2 && $_SESSION['user_type'] != 3) {
        // Redirect non-admin users to the main site
        header("Location: /FYP/FYP/User/HomePage/homePage.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="review.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

    <?php include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="user-table">
            <div class="search-container">
                    <div class="search-box">
                        <form id="searchForm">
                            <input type="text" name="query" id="searchInput" placeholder="Enter ID Or Name For Search...">
                            <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form> 
                    </div>
                </div>
                
            <h3>Product Review</h3>
            <table>

                <tbody id="userTableBody">
                    <?php
                        $sql= "SELECT p.*, AVG(r.rating) AS avr_rating
                            FROM product p
                            LEFT JOIN review r ON p.product_id=r.product_id
                            WHERE p.deleted=0
                            GROUP BY p.product_id";

                        $result = $conn->query($sql);

                        while($row= $result->fetch_assoc()){
                            $user_rating = round($row['avr_rating']);

                            echo '<tr>
                                    <td><img src="../../upload/'.$row['product_img1'].'"
                                    </td>
                                    <td><span class="name">'.$row['product_name'].'<span></td>
                                    <td><div class="rating">';
                                            for ($i = 1; $i <= 5; $i++) {
                                                $filled = $i <= $user_rating ? 'filled' : '';
                                                echo "<span class='star $filled' data-value='$i'>&#9733;</span>";
                                            }
                            echo '</div>
                                    </td>
                                    <td>
                                            <a class="review-button" href="view_review.php?id='.$row['product_id'].'"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>';
                        }
                    ?>
                </tbody>
                
            </table>
                
        </div>

        
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault(); 

            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            fetch('search_review.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('userTableBody').innerHTML = html;
            }); 
        });
    </script>
</body>
</html>
