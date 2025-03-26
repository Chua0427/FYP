<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "verosports";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Fail Connect: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $mobile_number = $_POST["mobile_number"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $address = $_POST["address"];
        $postcode = $_POST["postcode"];
        $state = $_POST["state"];
        $city = $_POST["city"];
        $birthday_date = $_POST["birthday_date"];
        $gender = $_POST["gender"];
        $user_type = 2; 

        $upload = "../../upload/";

        $originalName = $_FILES['profile_image']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueFileName = time() . '.' . $fileExtension;
        $targetPath = $upload . $uniqueFileName;

        $sql = "INSERT INTO users (first_name, last_name, email, mobile_number, password, 
                address, postcode, state, city, birthday_date, gender, user_type, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssis", $first_name, $last_name, $email, $mobile_number, 
                        $password, $address, $postcode, $state, $city, $birthday_date, 
                        $gender, $user_type, $uniqueFileName);

        if ($stmt->execute()) {
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath);
            echo "<script>alert('Add Successfully!'); window.location.href='add.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        } else {
            echo "Failed to upload image.";
        }
        $conn->close();
?>

