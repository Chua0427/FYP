<?php
// Include authentication check
require_once __DIR__ . '/../auth_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db-connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from database using MySQLi
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // Changed to fetch_assoc() for MySQLi
$stmt->close();

// Determine user type label
$user_types = [
    '1' => 'Regular User',
    '2' => 'Premium User',
    '3' => 'Administrator'
];
$user_type_label = $user_types[$user['user_type']] ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - VeroSports</title>
    <link rel="stylesheet" href="Profile.css">
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image-container">
            <?php

             $upload_dir = '../../upload/';
    
            if (!empty($user['profile_image']) && file_exists($upload_dir . $user['profile_image'])) {
            echo '<img src="' . $upload_dir . htmlspecialchars($user['profile_image']) . '" alt="Profile Image" class="profile-image">';
            } else {
            echo '<div><img src="../../upload/default.jpg" class="profile-image"></div>';}
            ?>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h1>
                <div class="profile-meta">
                    Member since <?php echo date('F Y', strtotime($user['create_at'])); ?>
                    <span class="user-type"><?php echo $user_type_label; ?></span>
                </div>
                <div class="profile-actions">
                    <a href="edit-profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="change-password.php" class="btn btn-outline">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
        <div class="profile-details">
            <div class="detail-card">
                <h3>Personal Information</h3>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>  
                <div class="detail-row">
                    <div class="detail-label">Mobile</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['mobile_number']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Birthday</div>
                    <div class="detail-value"><?php echo date('F j, Y', strtotime($user['birthday_date'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['gender']); ?></div>
                </div>
            </div>
            
            <div class="detail-card">
                <h3>Address Information</h3>
                <div class="detail-row">
                    <div class="detail-label">Address</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['address']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">City</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['city']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">State</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['state']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Postcode</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['postcode']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/../Header_and_Footer/footer.php'; ?>
</body>
</html>