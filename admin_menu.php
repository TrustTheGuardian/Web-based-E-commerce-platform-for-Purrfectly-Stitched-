<?php

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/',$path);
$page = $components[2];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    
<section id="menu">
    <div class="logo">
        <img src="" alt="Purrfectly Stitch">
        <h2>Purrfectly Stitch</h2>
    </div>
    <div class="items">
        <li class="nav_item"><i class="bi bi-clipboard2-data-fill"></i>
            <a href="admin_dashboard.php" <?php if($page == "admin_dashboard.php"){echo "nav-link active";} else {echo "nav-link";}?> aria-current="page"> Dashboard</a>
        </li>
        <li class="nav_item"><i class="bi bi-file-earmark-text-fill"></i>
            <a href="admin_reports.php" <?php if($page == "admin_reports.php"){echo "nav-link active";} else {echo "nav-link";}?> aria-current="page"> Reports</a>
        </li>
        <li class="nav_item"><i class="bi bi-bag-check-fill"></i>
            <a href="admin_order.php" <?php if($page == "admin_orders.php"){echo "nav-link active";} else {echo "nav-link";}?> aria-current="page"> Purchase Order</a>
        </li>
        <li class="nav_item"><i class="bi bi-people-fill"></i>
            <a href="admin_users.php" <?php if($page == "admin_users.php"){echo "nav-link active";} else {echo "nav-link";}?> aria-current="page"> Users</a>
        </li>
        <li class="nav_item"><i class="bi bi-box2-heart-fill"></i>
            <a href="admin_products.php" <?php if($page == "admin_products.php"){echo "nav-link active";} else {echo "nav-link";}?> aria-current="page"> Products</a>
        </li>
    </div>
</section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>