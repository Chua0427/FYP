<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "verosports";
                
    $conn = new mysqli($servername, $username, $password, $dbname);
                
    if ($conn->connect_error) {
        die("Fail Connect: " . $conn->connect_error);
    }
?>