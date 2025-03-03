<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>

    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Menu bar -->
    <?php include 'admin_menu.php'; ?>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get the current page file name (without query parameters)
            let currentPage = window.location.pathname.split("/").pop().split("?")[0];

            // Get all menu list items
            let menuItems = document.querySelectorAll("#menu .items li");

            // Loop through each menu item
            menuItems.forEach(item => {
                let link = item.querySelector("a"); // Select the <a> inside <li>
        
                // Check if the link's href matches the current page
                if (link && link.getAttribute("href") === currentPage) {
                    item.classList.add("active"); // Add 'active' class to the <li>
                    link.style.color = "black";  // Ensure text color change
                }
            });
        });

    </script>
</body>
</html>