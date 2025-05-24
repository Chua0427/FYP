<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeroSports</title>
    <link rel="stylesheet" href="add.css">
    <link rel="stylesheet" href="../Header_And_Footer/header.css">
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../Header_And_Footer/header.php'; ?>

    <div class="contain">
        <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>

        <div class="form-container">
            <h2>Add New Admin</h2>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <div class="column1">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number:</label>
                        <input type="tel" name="mobile_number" id="mobile_number" required>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label style="display: flex; justify-content:space-between;">Password: (1 lower, 1 upper, 1 digit, 1 symbol, at least 8)<i class="fa-solid fa-eye" id="Password"></i></label>
                        <input type="password" name="password" id="password" required >
                        <div id="strength" style="display:block; font-size:14px; font-size:14px; text-align:center;"></div>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; justify-content:space-between;">Confirm Password:  <i class="fa-solid fa-eye" id="ConfirmPassword"></i></label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <span id="passwordError" style="color: red; display: none; text-align:center; font-size:14px;">Does not match with password</span>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group" style="flex: 1;">
                        <label>Address:</label>
                        <textarea type="text" name="address" required style="height:150px; margin-left: 10px; border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Post Code:</label>
                        <input type="text" name="postcode" id="postcode" required>
                    </div>
                    <div class="form-group">
                        <label>State:</label>
                        <select id="state" name="state" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>City:</label>
                        <select id="city" name="city" required>
                            <option value="">Select City</option>
                        </select>
                    </div>
                </div>

                <div class="column1">
                    <div class="form-group">
                        <label>Birthday Date:</label>
                        <input type="date" name="birthday_date" id="date" required>
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender" id="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Profile Image:</label>
                    <input type="file" name="profile_image" required>
                </div>

                <button type="submit" id="submit">Register Admin</button>
            </form>
        </div>
    </div>
    <script src="add.js"></script>
</body>
</html>