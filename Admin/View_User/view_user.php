<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="view_user.css">
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
            <h3>View User</h3>
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
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Postcode</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Birthday Date</th>
                    <th>Gender</th>
                </tr>
                <tbody id="userTableBody">
                <?php
                    $sql= "SELECT * FROM users WHERE user_type=1";
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
                                <td>'.$row['gender'].'</td>
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

            fetch('search_user.php', {
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