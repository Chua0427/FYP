<header>
    <img src="../Header_And_Footer/images/VeroSports-removebg-preview.png" class="logo">

    <div class="title">

    <?php
    

require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../protect.php';
include __DIR__ . '/../../connect_db/config.php';
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $current_page = basename($_SERVER['PHP_SELF']);
        $user_id=$_SESSION['user_id'];

        $sql="SELECT * FROM users WHERE user_id= $user_id";
        $resultuser= $conn->query($sql);
        while($rowuser= $resultuser->fetch_assoc()){
            $current_user_type = $rowuser['user_type'];
        }

        if ($current_user_type == 3) {
            echo '<h1>Superadmin</h1>';
        }
        else{
            echo '<h1>Admin</h1>';
        }
    ?>

    </div>
    
</header>
