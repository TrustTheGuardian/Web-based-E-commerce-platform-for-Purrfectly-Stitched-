<?php
// Connection
include 'db_connection.php'; // Ensure $con is available

// Fetch categories
$categories = mysqli_query($con, "SELECT * FROM product_category");

// Fetch selected category (if any)
$selectedCategory = isset($_GET['product_category_ID']) ? mysqli_real_escape_string($con, $_GET['product_category_ID']) : '';

// Fetch products (filter by category if selected)
$productQuery = "SELECT * FROM products";
if (!empty($selectedCategory)) {
    $productQuery .= " WHERE product_category_ID = '$selectedCategory'";
}
$products = mysqli_query($con, $productQuery);

// Build the sales report filters
$where = [];

if (!empty($selectedCategory)) {
    $where[] = "p.product_category_ID = '$selectedCategory'";
}

if (!empty($_GET['product_ID'])) {
    $selectedProduct = mysqli_real_escape_string($con, $_GET['product_ID']);
    $where[] = "p.product_ID = '$selectedProduct'";
}

if (!empty($_GET['month'])) {
    $month = mysqli_real_escape_string($con, $_GET['month']);
    $where[] = "MONTH(o.ordered_at) = '$month'";
}

if (!empty($_GET['year'])) {
    $year = mysqli_real_escape_string($con, $_GET['year']);
    $where[] = "YEAR(o.ordered_at) = '$year'";
}

// Combine WHERE clauses
$whereSQL = "";
if (!empty($where)) {
    $whereSQL = "WHERE " . implode(' AND ', $where);
}

// Pagination settings
$limit = 10; // results per page

// Current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch total rows for pagination
$countQuery = "
    SELECT COUNT(*) AS total
    FROM order_items oi
    JOIN orders o ON oi.order_ID = o.order_ID
    JOIN products p ON oi.product_ID = p.product_ID
    JOIN product_category pc ON p.product_category_ID = pc.product_category_ID
    $whereSQL
";
$countResult = mysqli_query($con, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch sales with LIMIT for current page
$salesQuery = "
    SELECT 
        pc.category_name,
        o.order_ID,
        p.product_title,
        oi.quantity,
        oi.price,
        o.ordered_at
    FROM order_items oi
    JOIN orders o ON oi.order_ID = o.order_ID
    JOIN products p ON oi.product_ID = p.product_ID
    JOIN product_category pc ON p.product_category_ID = pc.product_category_ID
    $whereSQL
    ORDER BY o.ordered_at DESC
    LIMIT $offset, $limit
";
$sales = mysqli_query($con, $salesQuery);

// Initialize total sales amount
$totalSales = 0;

// Months array
$months = [
    1 => "January", 2 => "February", 3 => "March", 4 => "April",
    5 => "May", 6 => "June", 7 => "July", 8 => "August",
    9 => "September", 10 => "October", 11 => "November", 12 => "December"
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- css link -->
    <link rel="stylesheet" href="css_files/admin_reports_styles.css">
</head>
<body>
    <div class="container">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="pictures/Purrfectly Stitch.png" alt="Purrfectly Stitched Logo">
                </div>
                <div class="close" id="close-btn">
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>

            <div class="sidebar">
                <a href="admin_dashboard.php"><i class="bi bi-grid-fill"></i><h3>Dashboard</h3></a>
                <a href="admin_users.php"><i class="bi bi-people-fill"></i><h3>Users</h3></a>
                <a href="admin_inventory_v2.php"><i class="bi bi-box2-heart-fill"></i><h3>Products</h3></a>
                <a href="admin_orders.php"><i class="bi bi-bag-check-fill"></i><h3>Orders</h3></a>
                <a href="admin_reports.php"><i class="bi bi-file-earmark-text-fill"></i><h3>Reports</h3></a>
            </div>
        </aside>
        <!-- end of aside -->

        <main>
    <h1>Get Sales Reports</h1>
    <form method="GET" action="">
        <div class="filter-bar">
            <div class="filter-item">
                <label for="category">Choose a category:</label>
                <div class="custom-select-wrapper">
                    <select id="category" name="product_category_ID" class="custom-select">
                        <option value="">All Categories</option>
                        <?php
                        while ($category = mysqli_fetch_assoc($categories)) {
                            $selected = ($selectedCategory == $category['product_category_ID']) ? "selected" : "";
                            echo "<option value='{$category['product_category_ID']}' $selected>{$category['category_name']}</option>";
                        }
                        ?>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>

            <div class="filter-item">
                <label for="product">Choose a Product:</label>
                <div class="custom-select-wrapper">
                    <select id="product" name="product_ID" class="custom-select">
                        <option value="">All Products</option>
                        <?php
                        while ($product = mysqli_fetch_assoc($products)) {
                            $selectedProduct = isset($_GET['product_ID']) ? $_GET['product_ID'] : '';
                            $selected = ($selectedProduct == $product['product_ID']) ? "selected" : "";
                            echo "<option value='{$product['product_ID']}' $selected>{$product['product_title']}</option>";
                        }
                        ?>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>

            <div class="filter-item">
                <label for="month">Month:</label>
                <div class="custom-select-wrapper">
                    <select id="month" name="month" class="custom-select">
                        <option value="">All Months</option>
                        <?php
                        foreach ($months as $num => $name) {
                            $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? "selected" : "";
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>

            <div class="filter-item">
                <label for="year">Year:</label>
                <div class="custom-select-wrapper">
                    <select id="year" name="year" class="custom-select">
                        <option value="">All Years</option>
                        <?php
                        $currentYear = date('Y');
                        $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
                        for ($y = 2023; $y <= $currentYear + 1; $y++) {
                            $selected = ($selectedYear == $y) ? "selected" : "";
                            echo "<option value='$y' $selected>$y</option>";
                        }
                        ?>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>

            <button class="filter-button" type="submit">Filter</button>
        </div>
    </form>

    <!-- REPORTS TABLE -->
    <div class="reports">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Date of Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($sales) > 0) {
                        while ($row = mysqli_fetch_assoc($sales)) {
                            $amount = $row['quantity'] * $row['price'];
                            $totalSales += $amount;

                            echo "<tr>
                                    <td>{$row['category_name']}</td>
                                    <td>{$row['order_ID']}</td>
                                    <td>{$row['product_title']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>₱ " . number_format($amount, 2) . "</td>
                                    <td>" . date('F d, Y', strtotime($row['ordered_at'])) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php
            if ($totalPages > 1) {
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo "<strong>$i</strong> ";
                    } else {
                        echo "<a href='?" . http_build_query(array_merge($_GET, ['page' => $i])) . "'>$i</a> ";
                    }
                }
            }
            ?>
        </div>
    </div>

    <div class="total-sales">
        <?php
        $monthName = (!empty($_GET['month'])) ? $months[(int)$_GET['month']] : date('F');
        $yearSelected = (!empty($_GET['year'])) ? $_GET['year'] : date('Y');
        ?>
        <h1>Total Sales for <?php echo $monthName . " " . $yearSelected; ?>:</h1>
        <h2>₱<?php echo number_format($totalSales, 2); ?></h2>
    </div>
</main>

        <!-- RIGHT TOP AREA -->
        <div class="right">
            <div class="top">
                <button id="menu-btn"><i class="bi bi-list"></i></button>
                <div class="theme-toggler">
                    <i class="bi bi-brightness-high-fill active"></i>
                    <i class="bi bi-moon-fill"></i>
                </div>
                <div class="log-out">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <h3>Log Out</h3>
                </div>
            </div>
        </div>
    </div>

        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" class="custom-modal">
            <div class="custom-modal-content">
                <p>Are you sure you want to log out?</p>
                <div class="custom-modal-buttons">
                    <button id="confirmLogout">Yes</button>
                    <button id="cancelLogout">No</button>
                </div>
            </div>
        </div>
        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const currentPage = window.location.pathname.split('/').pop();
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(link => {
            const linkPage = link.getAttribute('href').split('/').pop();
            link.classList.toggle('active', linkPage === currentPage);
        });

        const sideMenu = document.querySelector("aside");
        const menuBtn = document.querySelector("#menu-btn");
        const closeBtn = document.querySelector("#close-btn");
        const themeToggler = document.querySelector(".theme-toggler");

        menuBtn.addEventListener('click', () => sideMenu.style.display = 'block');
        closeBtn.addEventListener('click', () => sideMenu.style.display = 'none');

        themeToggler.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme-variables');
            themeToggler.querySelector('i:nth-child(1)').classList.toggle('active');
            themeToggler.querySelector('i:nth-child(2)').classList.toggle('active');
        });

                // Logout modal logic
                const logoutBtn = document.querySelector('.log-out');
        const logoutModal = document.getElementById('logoutModal');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');

        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'block';
        });

        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        confirmLogout.addEventListener('click', () => {
            window.location.href = "logout.php"; // Update as needed
        });

        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });

    </script>
</body>
</html>
