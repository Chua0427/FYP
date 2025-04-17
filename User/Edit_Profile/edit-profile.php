<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - VeroSports</title>
    <link rel="stylesheet" href="edit-profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="../HomePage/images/VeroSports.jpeg" class="logo">
        <div class="nav-links">
            <a href="edit-profile.php">Edit Profile</a>
            <a href="change-password.php">Change Password</a>
            <a href="#">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <h1>Edit Profile</h1>
        
        <div class="profile-header">
            <div class="profile-image-container">
                <img src="default-profile.jpg" alt="Profile Image" class="profile-image" id="profileImage">
                <button class="change-image-btn" onclick="document.getElementById('profileImageInput').click()">
                    <i class="fas fa-camera"></i>
                </button>
                <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
            </div>
            <div>
                <h2 id="displayName">User Name</h2>
                <p>Member since <span id="memberSince">Create Acc Date</span></p>
            </div>
        </div>
        
        <form id="editProfileForm">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="mobileNumber">Mobile Number</label>
                        <input type="tel" id="mobileNumber" name="mobile_number" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" required>
                    </div>
                </div>
<<<<<<< HEAD
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
=======
                <div class="form-col">
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                </div>
>>>>>>> a95b9eca4863135b0f7a7228aaf3e3a942d6a4d0
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday_date" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="../HomePage/homePage.php" class="btn btn-cancel">Cancel</a>
                <div>
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
    
    <script src="edit-profile.js"></script>
</body>
</html>