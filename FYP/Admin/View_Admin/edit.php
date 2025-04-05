<?php
session_start();
include __DIR__ . '/../../connect_db/config.php';

if (isset($_GET['id'])) {
    $admin_id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE user_id = $admin_id";
    $result = $conn->query($sql);
    $admin = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $address = $_POST["address"];
    $postcode = $_POST["postcode"];
    $state = $_POST["state"];
    $city = $_POST["city"];
    $birthday_date = $_POST["birthday_date"];
    $gender = $_POST["gender"];
    $user_type = 2;

    if(!empty($_POST["password"])){
        $password= password_hash($_POST["password"], PASSWORD_DEFAULT);
        $password_sql= ", password= '$password'";
    }
    else{
        $password_sql="";
    }

    $profile_image_sql = '';

    $sql_old_image = "SELECT profile_image FROM users WHERE user_id = $admin_id";
    $result_old_image = $conn->query($sql_old_image);
    $row_old_image = $result_old_image->fetch_assoc();
    $old_profile_image = $row_old_image['profile_image'] ?? '';

    if (!empty($_FILES['profile_image']['name'])) {
        $upload = "../../upload/";
        $originalName = $_FILES['profile_image']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueFileName = time() . '.' . $fileExtension;
        $targetPath = $upload . $uniqueFileName;


        if (!empty($old_profile_image) && file_exists($upload . $old_profile_image)) {
            unlink($upload . $old_profile_image);
        }

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $profile_image_sql = ", profile_image='$uniqueFileName'";
        }
    }

    $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', mobile_number='$mobile_number' $password_sql ,address='$address' , postcode='$postcode' , state='$state' , city='$city', birthday_date= '$birthday_date', gender= '$gender', user_type ='$user_type' $profile_image_sql WHERE user_id=$admin_id";
    if ($conn->query($sql)) {
        echo "<script>alert('Edit Successfully!'); window.location.href='view_admin.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="../Add_Admin/add.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
            <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

            <div class="form-container">
                <h2>Edit Admin</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="column1">
                        <div class="form-group">
                            <label>First Name:</label>
                            <input type="text" name="first_name" value="<?php echo ($admin['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" name="last_name" value="<?php echo ($admin['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="column1">
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo ($admin['email']); ?>" required>
                            <span id="emailError" style="color: red; display: none; text-align: center; font-size: 14px;">Please Enter Valid Email...</span>
                        </div>
                        <div class="form-group">
                            <label>Mobile Number:</label>
                            <input type="tel" name="mobile_number" id="mobile_number" value="<?php echo ($admin['mobile_number']); ?>" required>
                            <span id="telError" style="color: red; display: none; text-align: center; font-size: 14px;">Please Enter Valid Mobile Number...</span>
                        </div>
                    </div>

                    <div class="column1">
                        <div class="form-group">
                            <label style="display: flex; justify-content:space-between;">Password (leave empty to keep current): <i class="fa-solid fa-eye" id="Password"></i></label>
                            <input type="password" name="password" id="password">
                            <div id="strength" style="display:block; font-size:14px; font-size:14px; text-align:center;"></div>
                        </div>
                        <div class="form-group">
                            <label style="display: flex; justify-content:space-between;">Confirm Password:  <i class="fa-solid fa-eye" id="ConfirmPassword"></i></label>
                            <input type="password" name="confirm_password" id="confirm_password">
                            <span id="passwordError" style="color: red; display: none; text-align:center; font-size:14px;">Does not match with password</span>
                        </div>
                    </div>

                    <div class="column1">
                        <div class="form-group" style="flex: 1;">
                            <label>Address:</label>
                            <textarea type="text" name="address"  required style="height:150px; margin-left: 10px; border-radius: 8px;"><?php echo ($admin['address']); ?></textarea>
                        </div>
                    </div>

                    <div class="column1">
                        <div class="form-group">
                            <label>Post Code:</label>
                            <input type="text" name="postcode" id="postcode" value="<?php echo ($admin['postcode']); ?>" required>
                            <span id="postcodeError" style="color:red; display:none; text-align:center; font-size:14px;">Please Enter Valid Postcode...</span>
                        </div>
                        <div class="form-group">
                            <label>State:</label>
                            <select id="state" name="state" required>
                                <option value="">Select State</option>
                                <option value="<?= ($admin['state']); ?>" selected><?= ($admin['state']); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>City:</label>
                            <select id="city" name="city" required>
                                <option value="">Select City</option>
                                <option value="<?= ($admin['city']); ?>" selected><?= ($admin['city']); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="column1">
                        <div class="form-group">
                            <label>Birthday Date:</label>
                            <input type="date" name="birthday_date" id="date" value="<?php echo ($admin['birthday_date']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Gender:</label>
                            <select name="gender" id="gender" required>
                                <option value="<?= ($admin['gender']); ?>" selected><?= ($admin['gender']); ?></option>

                                <?php 
                                    if($admin['gender']== "Male"){
                                        echo '<option value="Female">Female</option>';
                                    }
                                    else{
                                        echo '<option value="Male">Male</option>';
                                    }
                                ?>
                                
                                
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Profile Image:</label>
                        <input type="file" name="profile_image">
                    </div>

                    <button type="submit" id="submit">Edit Admin</button>
                </form>
            </div>
        </div>
        <script src="edit.js"></script>
</body>
</html>

