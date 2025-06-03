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

    <?php 

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php'; ?>

<body>
    <?php include __DIR__ . '/..//Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="user-table">
            <h3>View Customer Message</h3>
            <div class="sorting">
                <button id="sortTime" onclick="triggerSort()">Sort by Time</button>
            </div>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Contact At</th>
                </tr>

                <tbody id="userTableBody">
                    <?php
                        $sql= "SELECT* FROM contact_us c
                            JOIN users u ON c.user_id=u.user_id";

                        $result = $conn->query($sql);

                        while($row= $result->fetch_assoc()){
                            $image = !empty($row['profile_image']) ? $row['profile_image'] : 'default.jpg';
                            echo '<tr>
                                    <td><img src="../../upload/'.$image.'"</td>
                                    <td>'.$row['first_name']." ".$row['last_name'].'</td>
                                    <td>'.$row['email'].'</td>
                                    <td>'.$row['message'].'</td>
                                    <td>'.$row['contact_at'].'</td>
                                </tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let timeSortAsc = true;
        let originalRows=[];
        let tbody;

        document.addEventListener('DOMContentLoaded',function(){
            tbody=document.getElementById('userTableBody');
            originalRows=Array.from(tbody.rows);
        });

        function triggerSort(){

            originalRows.sort((a,b)=>{
                const timeA= new Date(a.children[4].textContent.trim());
                const timeB= new Date(b.children[4].textContent.trim());
                return timeSortAsc ? timeB-timeA : timeA-timeB;
            });

            originalRows.forEach(row => tbody.appendChild(row));
            
            timeSortAsc = !timeSortAsc;
            document.getElementById("sortTime").innerHTML = timeSortAsc ? "Sort by Time" : "Sort by Time ▲";

        }
    </script>
</body>
</html>