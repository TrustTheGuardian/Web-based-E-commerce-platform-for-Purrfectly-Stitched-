<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = end($components);

// Debugging
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

<div id="interface">
    <section id="topbar">
        <div class="navigation">
            <div class="n1">
                <div>
                    <i id="menu_icon" class="bi bi-list"></i>
                </div>
                <div class="search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search">
                </div>
            </div>
            <div class="profile">
                <i class="bi bi-bell"></i>
                <i class="bi bi-person-circle"></i>
            </div>
        </div>
    </section>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const menuIcon = document.getElementById("menu_icon");
    const menu = document.getElementById("menu");
    const interface = document.getElementById("interface");

    function toggleMenu() {
        menu.classList.toggle("active");

        if (menu.classList.contains("active")) {
            interface.style.marginLeft = "270px"; // Sidebar open
        } else {
            interface.style.marginLeft = "0"; // Sidebar closed
        }
    }

    if (menuIcon && menu) {
        menuIcon.addEventListener("click", toggleMenu);
    } else {
        console.error("Menu or icon not found!");
    }
});

</script>

</body>
</html>
