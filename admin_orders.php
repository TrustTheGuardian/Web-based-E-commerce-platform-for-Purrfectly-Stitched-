<?php
// Include your database connection
require_once 'db_connection.php';

// Initialize variables for filters
$order_date = isset($_GET['order_date']) ? $_GET['order_date'] : '';
$order_status = isset($_GET['order_status']) ? $_GET['order_status'] : '';

// Pagination settings
$limit = 10;  // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Current page
$offset = ($page - 1) * $limit;  // Start record

// Base query to fetch orders
$sql = "SELECT o.order_ID, o.ordered_at, o.total_price, o.order_status, u.FirstName, u.LastName
        FROM orders o
        JOIN users u ON o.user_ID = u.user_ID
        WHERE 1";

// Add conditions for date filter
if (!empty($order_date)) {
    $sql .= " AND DATE(o.ordered_at) = ?";
}

// Add conditions for status filter
if (!empty($order_status)) {
    $sql .= " AND o.order_status = ?";
}

// Add limit and offset for pagination
$sql .= " LIMIT ? OFFSET ?";

// Prepare and execute the query
$stmt = $con->prepare($sql);

// Bind parameters based on the filters
if (!empty($order_date) && !empty($order_status)) {
    // If both date and status filters are applied
    $stmt->bind_param("ssii", $order_date, $order_status, $limit, $offset);
} elseif (!empty($order_date)) {
    // If only the date filter is applied
    $stmt->bind_param("sii", $order_date, $limit, $offset);
} elseif (!empty($order_status)) {
    // If only the status filter is applied
    $stmt->bind_param("sii", $order_status, $limit, $offset);
} else {
    // If no filters are applied
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$order_result = $stmt->get_result();

// Fetch the total number of orders (for pagination)
$total_sql = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.user_ID = u.user_ID WHERE 1";
if (!empty($order_date)) {
    $total_sql .= " AND DATE(o.ordered_at) = ?";
}
if (!empty($order_status)) {
    $total_sql .= " AND o.order_status = ?";
}
$total_stmt = $con->prepare($total_sql);

// Bind parameters for the total count query
if (!empty($order_date) && !empty($order_status)) {
    $total_stmt->bind_param("ss", $order_date, $order_status);
} elseif (!empty($order_date)) {
    $total_stmt->bind_param("s", $order_date);
} elseif (!empty($order_status)) {
    $total_stmt->bind_param("s", $order_status);
}

$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- css link -->
    <link rel="stylesheet" href="css_files/admin_orders_styles.css">
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
                <a href="admin_content.php" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside -->

        <main>
            <h1>Manage Orders</h1>
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search by Order ID or Customer Name">
                <button class="btn-search">Search</button>
            </div>
        
            <b>Filter Orders by Date: </b>
            <div class="date">
                <form method="GET" action="">
                    <label for="order_date">Select Date:</label>
                    <input type="date" name="order_date" value="<?= htmlspecialchars($order_date) ?>" />

                    <label for="order_status">Select Status:</label>
                    <select name="order_status" style="padding:6px 12px; border:1px solid #ccc; border-radius:5px; background-color:#f9f9f9; font-size:14px;">
                        <option value="">-- All Statuses --</option>
                        <option value="Pending" <?= $order_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Completed" <?= $order_status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= $order_status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>

                    <button type="submit" style="padding:5px 10px; background-color:var(--color-primary); border:1px solid var(--color-light-gray); color:white;border-radius:var(--border-radius-1)">Apply</button>
                </form>
            </div>
        
            <!-- ORDER TABLE -->
            <div class="orders">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date Ordered</th>
                    <th>Products Ordered</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $order_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_ID']) ?></td>
                        <td><?= date('m/d/Y', strtotime($order['ordered_at'])) ?></td>
                        <td>
                            <?php
                            // Fetch products per order
                            $order_ID = $order['order_ID'];
                            $item_sql = "SELECT p.product_title, oi.quantity 
                                         FROM order_items oi
                                         JOIN products p ON oi.product_ID = p.product_ID
                                         WHERE oi.order_ID = ?";
                            $item_stmt = $con->prepare($item_sql);
                            $item_stmt->bind_param("i", $order_ID);
                            $item_stmt->execute();
                            $item_result = $item_stmt->get_result();

                            while ($item = $item_result->fetch_assoc()) {
                                echo htmlspecialchars($item['product_title']) . " (" . $item['quantity'] . "),<br>";
                            }
                            $item_stmt->close();
                            ?>
                        </td>
                        <td>₱ <?= number_format($order['total_price'], 2) ?></td>
                        <td class="warning">
                            <span class="status-text"><?= htmlspecialchars($order['order_status']) ?></span>
                            <small class="text-muted change-link" onclick="openOrderDetails(<?= $order['order_ID'] ?>)"> (details)</small>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&order_date=<?= htmlspecialchars($order_date) ?>&order_status=<?= htmlspecialchars($order_status) ?>"><?= $i ?></a>
                <?php if ($i < $total_pages) echo " | "; ?>
            <?php endfor; ?>
        </div>
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

    <!-- STATUS MODAL -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <?php
        // Only show if order is set (for example if using AJAX or PHP include)
        if (isset($_GET['order_id'])) {
            $order_id = intval($_GET['order_id']);

            // Fetch order and user info
            $modal_sql = "SELECT o.order_ID, o.payment_method, o.order_status, u.FirstName, u.LastName
                          FROM orders o
                          JOIN users u ON o.user_ID = u.user_ID
                          WHERE o.order_ID = ?";
            $modal_stmt = $con->prepare($modal_sql);
            $modal_stmt->bind_param("i", $order_id);
            $modal_stmt->execute();
            $modal_result = $modal_stmt->get_result();
            $order_info = $modal_result->fetch_assoc();

            if ($order_info):
        ?>
            <h2>Order Details</h2>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order_info['order_ID']) ?></p>
            <p><strong>Customer name:</strong> <?= htmlspecialchars($order_info['FirstName'] . ' ' . $order_info['LastName']) ?></p>
            
            <p><strong>Products Ordered:</strong>
                <?php
                // Fetch ordered products
                $product_sql = "SELECT p.product_title, oi.quantity 
                                FROM order_items oi 
                                JOIN products p ON oi.product_ID = p.product_ID
                                WHERE oi.order_ID = ?";
                $product_stmt = $con->prepare($product_sql);
                $product_stmt->bind_param("i", $order_id);
                $product_stmt->execute();
                $product_result = $product_stmt->get_result();

                while ($prod = $product_result->fetch_assoc()) {
                    echo htmlspecialchars($prod['product_title']) . " (x" . $prod['product_quantity'] . "), ";
                }
                $product_stmt->close();
                ?>
            </p>

            <p><strong>Payment:</strong> <?= ucfirst(htmlspecialchars($order_info['payment_method'])) ?></p>

            <p class="change-status"><strong>Change Order Status:</strong></p>
            <form action="update_order_status.php" method="POST">
                <input type="hidden" name="order_ID" value="<?= $order_info['order_ID'] ?>">
                <select name="order_status" id="statusSelect">
                    <option value="Pending" <?= $order_info['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Completed" <?= $order_info['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $order_info['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" id="saveStatus">Save</button>
            </form>
        <?php
            else:
                echo "<p>Order not found.</p>";
            endif;
        } else {
            echo "<p>No order selected.</p>";
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

        const modal = document.getElementById("statusModal");
        const closeModal = document.querySelector(".close-btn");
        const saveBtn = document.getElementById("saveStatus");
        const statusSelect = document.getElementById("statusSelect");
        let currentStatusText = null;

        document.querySelectorAll(".change-link").forEach(link => {
            link.addEventListener("click", function () {
                currentStatusText = this.previousElementSibling;
                statusSelect.value = currentStatusText.textContent.trim();
                modal.style.display = "block";
            });
        });

        closeModal.onclick = () => modal.style.display = "none";
        window.onclick = e => { if (e.target === modal) modal.style.display = "none"; }

        saveBtn.onclick = () => {
            if (currentStatusText) {
                const newStatus = statusSelect.value;
                const td = currentStatusText.closest("td");
                currentStatusText.textContent = newStatus;

                td.className = "";
                if (newStatus === "Completed") td.classList.add("success");
                else if (newStatus === "Pending") td.classList.add("warning");
                else if (newStatus === "Cancelled") td.classList.add("danger");

                modal.style.display = "none";
            }
        };

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

        function openOrderDetails(orderId) {
            // Open the modal
            document.getElementById('statusModal').style.display = 'block';

            // Use AJAX to fetch the order details (this assumes you're using PHP to display the modal)
            fetch('admin_order_details.php?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    // Update modal content
                    document.querySelector('#statusModal .modal-content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                });
        }

        // Close the modal when the close button is clicked
        document.querySelector('.close-btn').addEventListener('click', function() {
            document.getElementById('statusModal').style.display = 'none';
        });

    </script>
</body>
</html>
