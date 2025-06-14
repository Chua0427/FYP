<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin (user_type = 2 or user_type = 3)
    if ($_SESSION['user_type'] != 2 && $_SESSION['user_type'] != 3) {
        // Redirect non-admin users to the main site
        header("Location: /FYP/User/HomePage/homePage.php");
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
    <link rel="stylesheet" href="view_admin.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
    <?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php'; ?>
    
<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="admin-table">
            <h3>View Admin</h3>
            <div class="search-container">
                <div class="search-box">
                    <form id="searchForm">
                        <input type="text" name="query" id="searchInput" placeholder="Enter Keyword For Search...">
                        <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form> 
                </div>
            </div>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Postcode</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Birthday</th>
                    <th>Gender</th>

                    <?php
                        if ($current_user_type == 3) {
                            echo '<th>Edit/Delete</th>';
                        }
                    ?>
                    
                <tbody id="userTableBody">
                <?php
                    $sql= "SELECT * FROM users WHERE user_type = 2";
                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '<tr>
                                <td><img src="../../upload/'.$row['profile_image'].'"</td>
                                <td>'.$row['first_name']." ".$row['last_name'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['mobile_number'].'</td>
                                <td>'.$row['address'].'</td>
                                <td>'.$row['postcode'].'</td>
                                <td>'.$row['state'].'</td>
                                <td>'.$row['city'].'</td>
                                <td>'.$row['birthday_date'].'</td>
                                <td>'.$row['gender'].'</td>';


                                if ($current_user_type == 3) {
                                    echo '<td>
                                            <div class="button">
                                                <a href="edit_admin.php?id= '.$row['user_id'].' "class="btn btn-edit" id="edit"><i class="fa-solid fa-pen"></i></a>
                                                <a href="delete.php?id='. $row['user_id'].' "class="btn btn-delete" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                            
                                          </td>';
                                }
                        
                                echo '</tr>';
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

        fetch('search_admin.php', {
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