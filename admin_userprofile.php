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
            <a href="#"><i class="bi bi-bag-check-fill"></i><h3>Orders</h3></a>
            <a href="#"><i class="bi bi-file-earmark-text-fill"></i><h3>Reports</h3></a>
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
                            <td>{$order['order_id']}</td>
                            <td>{$order['product_name']}</td>
                            <td>{$order['quantity']}</td>
                            <td>₱{$order['total_amount']}</td>
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

<script>
// Your existing JS for sidebar and theme toggling
</script>
</body>
</html>