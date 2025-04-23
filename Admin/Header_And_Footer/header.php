<header>
    <img src="../Header_And_Footer/images/VeroSports-removebg-preview.png" class="logo">

    <div class="title">

    <?php
        $current_user_type = 2;

        if ($current_user_type == 3) {
            echo '<h1>Superadmin</h1>';
        }
        else{
            echo '<h1>Admin</h1>';
        }
    ?>

    </div>
        
   
</header>