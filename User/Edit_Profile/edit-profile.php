<?php
// Include authentication check
require_once __DIR__ . '/../auth_check.php';

// Session is already started in auth_check.php
require_once 'db-connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mobile_number = htmlspecialchars(trim($_POST['mobile_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $postcode = htmlspecialchars(trim($_POST['postcode']));
    $state = htmlspecialchars(trim($_POST['state']));
    $city = htmlspecialchars(trim($_POST['city']));
    $birthday_date = htmlspecialchars(trim($_POST['birthday_date']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    
    $profile_image = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../upload/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
            if ($profile_image && file_exists($upload_dir . $profile_image)) {
                unlink($upload_dir . $profile_image);
            }
            $profile_image = $new_filename;
        }
    }
    
    $sql = "UPDATE users SET 
            first_name = ?, 
            last_name = ?, 
            email = ?, 
            mobile_number = ?, 
            address = ?, 
            postcode = ?, 
            state = ?, 
            city = ?, 
            birthday_date = ?, 
            gender = ?, 
            profile_image = ? 
            WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", 
        $first_name, 
        $last_name, 
        $email, 
        $mobile_number, 
        $address, 
        $postcode, 
        $state, 
        $city, 
        $birthday_date, 
        $gender, 
        $profile_image, 
        $user_id
    );
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: edit-profile.php");
        exit();
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - VeroSports</title>
    <link rel="stylesheet" href="edit-profile.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form id="profileForm" action="edit-profile.php" method="POST" enctype="multipart/form-data">
            <div class="profile-picture-section">
                <div class="profile-picture">
                    <?php if ($user['profile_image']): ?>
                        <img src="../../upload/<?php echo $user['profile_image']; ?>" alt="Profile Image" id="profileImagePreview">
                    <?php else: ?>
                        <div class="default-profile" id="profileImagePreview"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                <label for="profile_image" class="change-photo-btn">Change Photo</label>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($user['postcode']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="birthday_date">Birthday</label>
                    <input type="date" id="birthday_date" name="birthday_date" value="<?php echo htmlspecialchars($user['birthday_date']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Save Changes</button>
                <a href="profile.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
    
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>

    <script>
        // Preview profile image before upload
        function previewImage(input) {
            const preview = document.getElementById('profileImagePreview');
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onloadend = function() {
                if (preview.tagName === 'IMG') {
                    preview.src = reader.result;
                } else {
                    // Replace default profile div with image
                    const img = document.createElement('img');
                    img.id = 'profileImagePreview';
                    img.src = reader.result;
                    img.alt = 'Profile Preview';
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const mobile = document.getElementById('mobile_number').value;
            
            // Simple email validation
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address');
                e.preventDefault();
                return;
            }
            
            // Simple mobile validation (adjust regex as needed)
            if (!/^[\d\s\-+]{10,15}$/.test(mobile)) {
                alert('Please enter a valid mobile number');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
