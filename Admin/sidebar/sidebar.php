<?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $current_user_type = 3; 
?>
<div class="sidebar-wrapper">
    <div class="sidebar">
        <ul>
            <li class="tab-button <?php if ($current_page == 'dashboard.php') echo 'active'; ?>">
                <a href="../Dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>

            <?php
                if ($current_user_type == 3) {
                    echo '<li class="tab-button ' . ($current_page == 'add.php' ? 'active' : '') . '">
                            <a href="../Add_Admin/add.php"><i class="fa-solid fa-user-plus"></i> Add Admin</a>
                          </li>';
                }
            ?>

            <li class="tab-button <?php if ($current_page == 'view_admin.php' || $current_page == 'edit_admin.php') echo 'active'; ?>">
                <a href="../View_Admin/view_admin.php"><i class="fas fa-user-shield"></i> Admin</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'view_user.php') echo 'active'; ?>">
                <a href="../View_User/view_user.php"><i class="fas fa-users"></i> User</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'billboard.php') echo 'active'; ?>">
                <a href="../Billboard/billboard.php"><i class="fas fa-image"></i> Billboard</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'view_product.php' || $current_page == 'view_stock.php' || $current_page == 'product.php' || $current_page == 'edit.php') echo 'active'; ?>">
                <a href="../Manage_Product/view_product.php"><i class="fas fa-box"></i> ManageProduct</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'view_order.php' || $current_page == 'view_order_item.php') echo 'active'; ?>">
                <a href="../View_Order/view_order.php"><i class="fas fa-shopping-cart"></i> View Order</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'view_delivery.php') echo 'active'; ?>">
                <a href="../Delivery_Status/view_delivery.php"><i class="fa-solid fa-truck"></i> Delivery Status</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'view_payment.php') echo 'active'; ?>">
                <a href="../View_Payment/view_payment.php"><i class="fas fa-credit-card"></i> View Payment</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'contact_us.php') echo 'active'; ?>">
                <a href="../Contact_Us/contact_us.php"><i class="fas fa-headset"></i> Customer Support</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'review.php' || $current_page == 'view_review.php') echo 'active'; ?>">
                <a href="../Review/review.php"><i class="fas fa-star"></i> Review</a>
            </li>
            <li class="tab-button <?php if ($current_page == 'generate_report.php') echo 'active'; ?>">
                <a href="#"><i class="fas fa-chart-line"></i> Generate Report</a>
            </li>
        </ul>
    </div>
</div>


<script>
    function active(event){
        const button=document.querySelectorAll(".tab-button");
        button.forEach(button => {
            button.classList.remove("active");
        });

        event.currentTarget.classList.add("active");

    }
</script>