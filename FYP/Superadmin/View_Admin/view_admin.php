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

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.html'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.html'; ?>

        <div class="admin-table">
            <h3>View Admin</h3>
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
                    <th>Edit/Delete</th>
                </tr>
                <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "verosports";
                
                    $conn = new mysqli($servername, $username, $password, $dbname);
                
                    if ($conn->connect_error) {
                        die("Fail Connect: " . $conn->connect_error);
                    }

                    $sql= "SELECT * FROM users";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

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
                                <td>
                                <button id="edit">Edit</button>
                                <button id="delete">Delete</button>
                                </td>
                            </tr>';
                    }
                    ?>
                
            </table>
                
        </div>

        
    </div>
</body>
</html>