<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = end($components); // Get the last part of the URL

// Debugging (Check in Browser Console)
echo "<script>console.log('Current Page: " . $page . "');</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS Link -->
    <link rel="stylesheet" href="css_files/admin_menu_and_topbar.css">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
<section id="menu">
    <div class="logo">
        <img src="" alt="Purrfectly Stitch">
        <h2>Purrfectly Stitch</h2>
    </div>
    <ul class="items">  <!-- Changed from div to ul for proper structure -->
        <li class="nav_item <?php echo ($page == 'admin_dashboard.php' || $page == 'admin_dashboard') ? 'active' : ''; ?>">
            <a href="admin_dashboard.php" class="nav-link">
                <i class="bi bi-clipboard2-data-fill"></i> Dashboard
            </a>
        </li>
        
        <li class="nav_item <?php echo ($page == 'admin_reports.php') ? 'active' : ''; ?>">
            <a href="admin_reports.php" class="nav-link">
                <i class="bi bi-file-earmark-text-fill"></i> Reports
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_orders.php' || $page == 'admin_orders.php') ? 'active' : ''; ?>">
            <a href="admin_orders.php" class="nav-link">
                <i class="bi bi-bag-check-fill"></i> Purchase Order
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_users.php') ? 'active' : ''; ?>">
            <a href="admin_users.php" class="nav-link">
                <i class="bi bi-people-fill"></i> Users
            </a>
        </li>

        <li class="nav_item <?php echo ($page == 'admin_products.php') ? 'active' : ''; ?>">
            <a href="admin_inventory.php" class="nav-link">
                <i class="bi bi-box2-heart-fill"></i> Inventory
            </a>
        </li>
    </ul>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
