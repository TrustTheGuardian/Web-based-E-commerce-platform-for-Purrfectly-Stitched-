<?php
include 'db_connection.php';

if (!isset($_GET['user_ID'])) {
    echo "User ID not provided.";
    exit;
}

$id = $_GET['user_ID'];

// Prevent SQL Injection
$id = mysqli_real_escape_string($con, $id);

$query = "SELECT * FROM users WHERE user_ID = '$id'";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "User not found.";
    exit;
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css_files/admin_profile_styles.css">
</head>
<body>
<div class="container">
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
            <a href="admin_dashboard.html"><i class="bi bi-grid-fill"></i><h3>Dashboard</h3></a>
            <a href="admin_users.html"><i class="bi bi-people-fill"></i><h3>Users</h3></a>
            <a href="admin_inventory_v2.html"><i class="bi bi-box2-heart-fill"></i><h3>Products</h3></a>
            <a href="admin_orders.html"><i class="bi bi-bag-check-fill"></i><h3>Orders</h3></a>
            <a href="admin_reports.html"><i class="bi bi-file-earmark-text-fill"></i><h3>Reports</h3></a>
        </div>
    </aside>

    <main>
        <h1>User Profile</h1>
        <div class="profile">
            <div class="dp">
                <div class="user-profile">
                    <img src="pictures/default-avatar.png" alt="profile-photo">
                </div>
            </div>
            <div class="details">
                <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></p>
                <p><strong>Mobile Number:</strong> <?= htmlspecialchars($user['Mobile']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($user['Address']) ?></p>
                <p><strong>Account Created:</strong> <?= htmlspecialchars($user['CreatedAt']) ?></p>
                <!-- <p><strong>Account Updated:</strong> <?= htmlspecialchars($user['updated_at']) ?></p> -->
                <!-- <p><strong>Account Status:</strong> 
                    <span class="<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                        <?= ucfirst($user['status']) ?>
                    </span> -->
                </p>
            </div>
        </div>

        <div class="purchase-history">
            <h2>Transaction History</h2>
            <table class="purchase-history-table">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Shipping Address</th>
                    <th>Date of Order</th>
                </tr>
                </thead>
                <tbody>
                <!-- <?php
                $orders = mysqli_query($con, "SELECT * FROM orders WHERE user_id = '$id'");
                while ($order = mysqli_fetch_assoc($orders)) {
                    echo "<tr>
                            <td>{$order['order_ID']}</td>
                            <td>{$order['product_title']}</td>
                            <td>{$order['order_quantity']}</td>
                            <td>₱{$order['product_total_amount']}</td>
                            <td>{$order['shipping_address']}</td>
                            <td>{$order['order_date']}</td>
                          </tr>";
                }
                ?> -->
                </tbody>
            </table>
        </div>

        <div class="actions">
            <span class="action-link delete">Delete</span>
            <span class="action-link ban">Ban</span>
            <span class="action-link unban" style="display: none;">Unban</span>
        </div>
    </main>

    <div class="right">
        <div class="top">
            <button id="menu-btn"><i class="bi bi-list"></i></button>
            <div class="theme-toggler">
                <i class="bi bi-brightness-high-fill active"></i>
                <i class="bi bi-moon-fill"></i>
            </div>
            <div class="log-out">
                <i class="bi bi-box-arrow-in-right"></i><h3>Log Out</h3>
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
        
    </script>
</body>
</html>