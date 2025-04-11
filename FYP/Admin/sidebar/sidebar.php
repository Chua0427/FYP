<div class="sidebar-wrapper">
    <div class="sidebar">
        <ul>
            <li><a href="../Dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>

            <?php
                $current_user_type = 3; /*session_start();
                                            $current_user_type = $_SESSION['user_type'];*/

                if ($current_user_type == 3) {
                    echo '<li><a href="../Add_Admin/add.php"><i class="fa-solid fa-user-plus"></i>Add Admin</a></li>';
                }
            ?>


            <li><a href="../View_Admin/view_admin.php"><i class="fas fa-user-shield"></i> Admin</a></li>
            <li><a href="../View_User/view_user.php"><i class="fas fa-users"></i> User</a></li>
            <li><a href="../Billboard/billboard.php"><i class="fas fa-image"></i> Billboard</a></li>
            <li><a href="../Manage_Product/view_product.php"><i class="fas fa-box"></i> ManageProduct</a></li>
            <li><a href=""><i class="fas fa-shopping-cart"></i> View Order</a></li>
            <li><a href=""><i class="fas fa-list"></i> View Order Items</a></li>
            <li><a href=""><i class="fas fa-credit-card"></i> View Payment</a></li>
            <li><a href=""><i class="fas fa-headset"></i> Customer Support</a></li>
            <li><a href=""><i class="fas fa-star"></i> Review</a></li>
            <li><a href=""><i class="fas fa-chart-line"></i> Generate Report</a></li>
        </ul>
    </div>
</div>