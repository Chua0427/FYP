<?php
    include __DIR__ . '/../../connect_db/config.php';

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
        $user_type = 2; // Admin user type

        $upload = "../../upload/";

        $originalName = $_FILES['profile_image']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueFileName = time() . '.' . $fileExtension;
        $targetPath = $upload . $uniqueFileName;

        $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkEmailSql);

            if ($result->num_rows > 0) {
                echo "<script>alert('Email already exists! Please use another email.'); window.location.href='add.php';</script>";
            }
            else{
                $sql = "INSERT INTO users (first_name, last_name, email, mobile_number, password, 
                    address, postcode, state, city, birthday_date, gender, user_type, profile_image) 
                    VALUES ('$first_name', '$last_name', '$email', '$mobile_number', '$password', 
                    '$address', '$postcode', '$state', '$city', '$birthday_date', '$gender', 
                    '$user_type', '$uniqueFileName')";

                $result = $conn->query($sql);

                if ($result) {
                    move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath);
                    echo "<script>alert('Add Successfully!'); window.location.href='add.php';</script>";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        }
        else {
            echo "Failed to upload image.";
        }
        $conn->close();
?>

