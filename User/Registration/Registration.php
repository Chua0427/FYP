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
    $raw_password = $_POST["password"];
    $passwordError = null;
    $birthday = new DateTime($_POST["birthday_date"]);
    $today = new DateTime();
    $age = $today->diff($birthday)->y;
    
    if ($age < 18) {
        die("<script>
            alert('You must be at least 18 years old to register.');
            window.history.back();
            </script>");
    }
    if (strlen($raw_password) < 8) {
        $passwordError = "Password must be at least 8 characters";
    } elseif (!preg_match("/[A-Z]/", $raw_password)) {
        $passwordError = "Password must contain at least one uppercase letter";
    } elseif (!preg_match("/\d/", $raw_password)) {
        $passwordError = "Password must contain at least one number";
    } elseif (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $raw_password)) {
        $passwordError = "Password must contain at least one special character";
    }

    if ($passwordError) {
        die("<script>
            alert('Password Error: $passwordError');
            window.history.back();
            </script>");
    }
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
    $user_type = 1; 
    
    $profile_image = null;
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        // Email already exists
        echo "<script>
                alert('This email is already registered. Please use a different email.');
                window.history.back();
              </script>";
        exit();
    }
    
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $upload = "../../upload/";
        $originalName = $_FILES['profile_image']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueFileName = time() . '.' . $fileExtension;
        $targetPath = $upload . $uniqueFileName;
        
        if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $profile_image = $uniqueFileName;
        }
    }

    $sql = "INSERT INTO users (first_name, last_name, email, mobile_number, password, 
            address, postcode, state, city, birthday_date, gender, profile_image, user_type, create_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

    $stmt = $conn->prepare($sql);
    if(!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssssssssssssi", $first_name, $last_name, $email, $mobile_number, 
                    $password, $address, $postcode, $state, $city, $birthday_date, 
                    $gender, $profile_image, $user_type);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!'); window.location.href='../login/login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>