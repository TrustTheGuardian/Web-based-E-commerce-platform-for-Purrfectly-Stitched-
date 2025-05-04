<?php
include 'db_connection.php';

// Handle selected filter or custom range
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : null;

// Prepare WHERE conditions separately
$whereConditionOrders = "";
$whereConditionUsers = "";

if (!empty($fromDate) && !empty($toDate)) {
    // Custom Range Selected
    $whereConditionOrders = "DATE(ordered_at) BETWEEN '$fromDate' AND '$toDate'";
    $whereConditionUsers = "DATE(CreatedAt) BETWEEN '$fromDate' AND '$toDate'";
    $filter = 'custom_range';
} else {
    // Predefined Filters
    if ($filter == 'today') {
        $whereConditionOrders = "DATE(ordered_at) = CURDATE()";
        $whereConditionUsers = "DATE(CreatedAt) = CURDATE()";
    } elseif ($filter == 'this_week') {
        $whereConditionOrders = "YEARWEEK(ordered_at, 1) = YEARWEEK(CURDATE(), 1)";
        $whereConditionUsers = "YEARWEEK(CreatedAt, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filter == 'this_month') {
        $whereConditionOrders = "MONTH(ordered_at) = MONTH(CURDATE()) AND YEAR(ordered_at) = YEAR(CURDATE())";
        $whereConditionUsers = "MONTH(CreatedAt) = MONTH(CURDATE()) AND YEAR(CreatedAt) = YEAR(CURDATE())";
    } else {
        // Specific selected date fallback
        $whereConditionOrders = "DATE(ordered_at) = '$selectedDate'";
        $whereConditionUsers = "DATE(CreatedAt) = '$selectedDate'";
    }
}

// Queries
$salesQuery = "
    SELECT SUM(total_price) AS total_sales 
    FROM orders 
    WHERE order_status = 'Completed' AND $whereConditionOrders
";
$salesResult = mysqli_query($con, $salesQuery);
$totalSales = mysqli_fetch_assoc($salesResult)['total_sales'] ?? 0;

$ordersQuery = "
    SELECT COUNT(*) AS total_orders 
    FROM orders 
    WHERE $whereConditionOrders
";
$ordersResult = mysqli_query($con, $ordersQuery);
$totalOrders = mysqli_fetch_assoc($ordersResult)['total_orders'] ?? 0;

$usersQuery = "
    SELECT COUNT(*) AS total_users 
    FROM users 
    WHERE $whereConditionUsers
";
$usersResult = mysqli_query($con, $usersQuery);
$totalUsers = mysqli_fetch_assoc($usersResult)['total_users'] ?? 0;

// Recent Orders (always latest, no filter)
$recentOrdersQuery = "
    SELECT o.order_ID, u.FirstName, u.LastName, o.payment_method, o.order_status
    FROM orders o
    JOIN users u ON o.user_ID = u.user_ID
    ORDER BY o.ordered_at DESC
    LIMIT 5
";
$recentOrders = mysqli_query($con, $recentOrdersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

        <!-- Bootstrap CDN
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     -->
        <!-- Bootstrap Icons CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

        <!-- css link -->
        <link rel="stylesheet" href="css_files/admin_styles.css">

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
                <a href="admin_dashboard.php" class="">
                    <i class="bi bi-grid-fill"></i>
                    <h3>Dashboard</h3>
                </a>
                <a href="admin_users.php" class="">
                    <i class="bi bi-people-fill"></i>
                    <h3>Users</h3>
                </a>
                <a href="admin_inventory_v2.php" class="">
                    <i class="bi bi-box2-heart-fill"></i>
                    <h3>Products</h3>
                </a>
                <a href="admin_orders.php" class="">
                    <i class="bi bi-bag-check-fill"></i>
                    <h3>Orders</h3>
                </a>
                <a href="admin_reports.php" class=""> 
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <h3>Reports</h3>
                </a>
                <a href="admin_content.html" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside / side bar -->

        <main>
    <h1>Dashboard</h1>
    <p>Welcome, Admin</p><br>

    <div class="date">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <div style="display:flex; gap:5px;">
                <button type="submit" name="filter" value="today" <?php echo $filter == 'today' ? 'style="background-color:#3c91e6;color:white;"' : ''; ?>>Today</button>
                <button type="submit" name="filter" value="this_week" <?php echo $filter == 'this_week' ? 'style="background-color:#3c91e6;color:white;"' : ''; ?>>This Week</button>
                <button type="submit" name="filter" value="this_month" <?php echo $filter == 'this_month' ? 'style="background-color:#3c91e6;color:white;"' : ''; ?>>This Month</button>
            </div>

            <div style="display:flex; gap:5px; align-items:center;">
                <label>From:</label>
                <input type="date" name="from_date" value="<?php echo $fromDate ?? ''; ?>" style="padding:5px;">
                <label>To:</label>
                <input type="date" name="to_date" value="<?php echo $toDate ?? ''; ?>" style="padding:5px;">
                <button type="submit" style="padding:5px 10px;">Apply</button>
            </div>
        </form>
    </div><br>

    <div class="insights">
        <div class="sales">
            <i class="bi bi-clipboard-data-fill"></i>
            <div class="middle">
                <div class="left">
                    <h3>Total Sales</h3>
                    <h1>₱<?php echo number_format($totalSales, 2); ?></h1>
                </div>
                <div class="progress">
                    <svg>
                        <circle cx="38" cy="38" r="36"></circle>
                    </svg>
                    <div class="number">
                        <p><?php echo $totalSales > 0 ? '100%' : '0%'; ?></p>
                    </div>
                </div>
            </div>
            <small class="text-muted">
                <?php
                echo $filter == 'custom_range' ? "$fromDate to $toDate" : ucfirst(str_replace('_', ' ', $filter));
                ?>
            </small>
        </div>

        <div class="total-orders">
            <i class="bi bi-cart4"></i>
            <div class="middle">
                <div class="left">
                    <h3>Total Orders</h3>
                    <h1><?php echo $totalOrders; ?></h1>
                </div>
                <div class="progress">
                    <svg>
                        <circle cx="38" cy="38" r="36"></circle>
                    </svg>
                    <div class="number">
                        <p><?php echo $totalOrders > 0 ? '100%' : '0%'; ?></p>
                    </div>
                </div>                        
            </div>
            <small class="text-muted">
                <?php
                echo $filter == 'custom_range' ? "$fromDate to $toDate" : ucfirst(str_replace('_', ' ', $filter));
                ?>
            </small>
        </div>

        <div class="users">
            <i class="bi bi-person-heart"></i>
            <div class="middle">
                <div class="left">
                    <h3>Total Users</h3>
                    <h1><?php echo $totalUsers; ?></h1>
                </div>
            </div>
            <small class="text-muted">
            <?php
                echo $filter == 'custom_range' ? "$fromDate to $toDate" : ucfirst(str_replace('_', ' ', $filter));
                ?>
            </small>
        </div>
    </div>
    <!-- END OF INSIGHTS -->

    <div class="recent-order">
        <h2>Recent Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Products Ordered</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($recentOrders) > 0) {
                    while ($order = mysqli_fetch_assoc($recentOrders)) {
                        $orderID = $order['order_ID'];

                        // Fetch ordered products
                        $productsQuery = "
                            SELECT p.product_title, oi.quantity
                            FROM order_items oi
                            JOIN products p ON oi.product_ID = p.product_ID
                            WHERE oi.order_ID = '$orderID'
                        ";
                        $productsResult = mysqli_query($con, $productsQuery);

                        $productsOrdered = [];
                        while ($product = mysqli_fetch_assoc($productsResult)) {
                            $productsOrdered[] = "{$product['product_title']} ({$product['quantity']})";
                        }
                        $productsList = implode(",<br>", $productsOrdered);

                        echo "<tr>
                                <td>{$order['order_ID']}</td>
                                <td>{$order['FirstName']} {$order['LastName']}</td>
                                <td>$productsList</td>
                                <td>{$order['payment_method']}</td>
                                <td class='warning'>{$order['order_status']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>No recent orders</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="">Show All...</a>
    </div>
</main>
        <!-- END OF MAIN  -->

        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <i class="bi bi-list"></i>
                </button>
                <div class="theme-toggler">
                    <i class="bi bi-brightness-high-fill active"></i>
                    <i class="bi bi-moon-fill"></i>
                </div>
                <div class="log-out">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <h3>Log Out</h3>
                </div>
            </div>
            <!-- END OF TOP AREA -->
<div class="recent-notifications">
    <h2>Notifications</h2>
    <div class="notifications">
        <?php
        // Query: Get recent orders with user name and product names
        $notificationsQuery = "
            SELECT o.order_ID, o.ordered_at, u.FirstName, u.LastName, u.ProfileImage
            FROM orders o
            JOIN users u ON o.user_ID = u.user_ID
            ORDER BY o.ordered_at DESC
            LIMIT 5
        ";
        
        $notificationsResult = mysqli_query($con, $notificationsQuery);

        // Helper function to calculate "time ago"
        function getTimeAgo($timestamp) {
            $diff = time() - $timestamp;
            if ($diff < 60) {
                return $diff . " seconds ago";
            } elseif ($diff < 3600) {
                return floor($diff / 60) . " minutes ago";
            } elseif ($diff < 86400) {
                return floor($diff / 3600) . " hours ago";
            } else {
                return floor($diff / 86400) . " days ago";
            }
        }

        while ($notification = mysqli_fetch_assoc($notificationsResult)) {
            $orderID = $notification['order_ID'];
            $firstName = $notification['FirstName'];
            $lastName = $notification['LastName'];
            $orderedAt = $notification['ordered_at'];
            $profileImagePath = (!empty($notification['ProfileImage']) && file_exists($notification['ProfileImage']))
                ? $notification['ProfileImage']
                : 'pictures/default-avatar.png';
        
            // Get products
            $productsQuery = "
                SELECT p.product_title, oi.quantity
                FROM order_items oi
                JOIN products p ON oi.product_ID = p.product_ID
                WHERE oi.order_ID = '$orderID'
            ";
            $productsResult = mysqli_query($con, $productsQuery);
            $productsList = [];
            while ($product = mysqli_fetch_assoc($productsResult)) {
                $productsList[] = $product['product_title'] . " (x" . $product['quantity'] . ")";
            }
            $productsString = implode(", ", $productsList);
        
            // Time difference
            $timeAgo = getTimeAgo(strtotime($orderedAt));
        
            echo '
            <div class="notification">
                <div class="profile-photo">
                    <img src="' . htmlspecialchars($profileImagePath) . '" alt="profile-photo" style="width: 40px; height: 40px; object-fit: cover;">
                </div>
                <div class="message">
                    <p><b>' . htmlspecialchars($firstName . ' ' . $lastName) . '</b> placed an order for: ' . htmlspecialchars($productsString) . '</p>
                    <small class="text-muted">' . $timeAgo . '</small>
                </div>
            </div>';
        }
        ?>
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

        // Get current page URL (excluding query strings and hashes)
        const currentPage = window.location.pathname.split('/').pop();

        // Get all sidebar links
        const sidebarLinks = document.querySelectorAll('.sidebar a');

        sidebarLinks.forEach(link => {
            // Extract the filename from the href attribute
            const linkPage = link.getAttribute('href').split('/').pop();

            // If it matches the current page, add 'active' class
            if (linkPage === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        const sideMenu = document.querySelector("aside");
        const menuBtn = document.querySelector("#menu-btn");
        const closeBtn = document.querySelector("#close-btn")
        const themeToggler = document.querySelector(".theme-toggler")

        //show sidebar
        menuBtn.addEventListener('click', () => {
            sideMenu.style.display = 'block';
        })

        //close sidebar
        closeBtn.addEventListener('click', () => {
            sideMenu.style.display = 'none';
        })

        //change theme
        themeToggler.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme-variables')
        
            themeToggler.querySelector('i:nth-child(1)').classList.toggle('active');
            themeToggler.querySelector('i:nth-child(2)').classList.toggle('active');
        })

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