<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="contact_us.css">
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
            <h3>View Customer Message</h3>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Contact At</th>
                </tr>
                <?php
                    $sql= "SELECT* FROM contact_us c
                           JOIN users u ON c.user_id=u.user_id";

                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '<tr>
                                <td><img src="../../upload/'.$row['profile_image'].'"</td>
                                <td>'.$row['first_name']." ".$row['last_name'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['message'].'</td>
                                <td>'.$row['contact_at'].'</td>
                            </tr>';
                    }
                    ?>
                
            </table>
                
        </div>

        
    </div>
</body>
</html>