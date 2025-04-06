<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = end($components);

// Debugging
echo "<script>console.log('Current Page: " . $page . "');</script>";
?>

    <div class="navigation">
        <div class="n1">
            <div>
                <i id="menu_icon" class="bi bi-list"></i>
            </div>
        </div>
        <div class="profile">
            <i class="bi bi-bell"></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>
