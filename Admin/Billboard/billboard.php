<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="billboard.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="table">
            <table>
                <tr>
                    <th>Billboard Image</th>
                    <th>Delete</th>
                </tr>

                <?php
                    include __DIR__. '/../../connect_db/config.php';

                    $sql = "SELECT * FROM billboard";
                    $result = $conn->query($sql);

                    while($row= $result->fetch_assoc()){
                        echo '<tr>
                                    <td>
                                        <img src="../../upload/'. $row['image'] .'" alt="">
                                    </td>
                                    <td>
                                        <a href="delete.php?id='. $row['billboard_id'].' "class="btn btn-delete" id="delete" onclick="return confirm(\'Are you sure?\')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>';
                    }
                ?>
                </tr>
            </table>
            
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <label for="billboard_img" class="custom-file-label"><i class="fa-solid fa-plus" style="margin-right: 10px;"></i>Add More</label>
                    <input type="file" id="billboard_img" name="billboard_img" class="file-input" required>
                    <div class="button">
                            <button type="submit" class="upload-btn">Upload</button>
                    </div>      
                </form>
        </div>
    </div>
</body>
</html>