<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = end($components); // Get the last part of the URL

// Debugging (Check in Browser Console)
echo "<script>console.log('Current Page: " . $page . "');</script>";
?>

<section id="menu">
    <div class="logo">
        <img src="pictures/Purrfectly Stitch.png" alt="Purrfectly Stitch" width="320px" height="200px">
    </div>
    <ul class="items">  <!-- Changed from div to ul for proper structure -->
        <li class="nav_item <?php echo ($page == 'admin_dashboard.php') ? 'active' : ''; ?>">

            <a href="admin_dashboard.php" class="nav-link">
                <i class="bi bi-clipboard2-data-fill"></i> Dashboard
            </a>
        </li>
        
        <li class="nav_item <?php echo ($page == 'admin_reports.php') ? 'active' : ''; ?>">
            <a href="admin_reports.php" class="nav-link">
                <i class="bi bi-file-earmark-text-fill"></i> Reports
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_orders.php') ? 'active' : ''; ?>">
            <a href="admin_orders.php" class="nav-link">
                <i class="bi bi-bag-check-fill"></i> Purchase Order
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_user.php') ? 'active' : ''; ?>">
            <a href="admin_user.php" class="nav-link">
                <i class="bi bi-people-fill"></i> Users
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_inventory.php') ? 'active' : ''; ?>">
            <a href="admin_inventory.php" class="nav-link">
                <i class="bi bi-box2-heart-fill"></i> Inventory
            </a>
        </li>
    </ul>
</section>
