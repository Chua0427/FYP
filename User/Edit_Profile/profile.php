<?php
session_start();
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
    <link rel="stylesheet" href="../Header_and_Footer/header.css">
    <link rel="stylesheet" href="../Header_and_Footer/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php include __DIR__ . '/../Header_and_Footer/header.php'; ?>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .profile-image-container {
            position: relative;
            margin-right: 30px;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--light-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            margin: 0;
            color: var(--dark-color);
            font-size: 28px;
        }
        
        .profile-meta {
            color: #7f8c8d;
            margin: 5px 0 15px;
        }
        
        .user-type {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .profile-actions {
            margin-top: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-outline {
            border: 1px solid var(--secondary-color);
            color: var(--secondary-color);
            background-color: transparent;
        }
        
        .btn-outline:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .detail-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .detail-card h3 {
            margin-top: 0;
            color: var(--dark-color);
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 10px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #7f8c8d;
            width: 120px;
        }
        
        .detail-value {
            flex: 1;
            color: var(--dark-color);
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-image-container {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .profile-actions {
                display: flex;
                flex-direction: column;
            }
            
            .btn {
                margin-right: 0;
                margin-bottom: 10px;
                width: 100%;
                text-align: center;
            }
        }
        </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image-container">
                <img src="<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default-profile.jpg'; ?>" 
                     alt="Profile Image" class="profile-image">
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