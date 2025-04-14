<?php

$user_rating = 5; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Rating</title>
    <style>
        .star {
            font-size: 30px;
            color: #ccc; 
            cursor: pointer;
        }

        .star.filled {
            color: gold; 
        }
    </style>
</head>
<body>
    <div class="rating">
        <?php
        for ($i = 1; $i <= 5; $i++) {
            $filled = $i <= $user_rating ? 'filled' : '';
            echo "<span class='star $filled' data-value='$i'>&#9733;</span>";
        }
        ?>
    </div>
</body>
</html>
