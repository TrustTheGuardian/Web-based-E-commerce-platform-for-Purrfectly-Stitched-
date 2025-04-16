<?php
include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Inventory</title>

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS Link -->
    <link rel="stylesheet" href="css_files/admin_inventory_styles.css">
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
            <a href="admin_dashboard.html"><i class="bi bi-grid-fill"></i><h3>Dashboard</h3></a>
            <a href="admin_users.php"><i class="bi bi-people-fill"></i><h3>Users</h3></a>
            <a href="admin_inventory_v2.php"><i class="bi bi-box2-heart-fill"></i><h3>Products</h3></a>
            <a href="#"><i class="bi bi-bag-check-fill"></i><h3>Orders</h3></a>
            <a href="#"><i class="bi bi-file-earmark-text-fill"></i><h3>Reports</h3></a>
        </div>
    </aside>
    <!-- END OF SIDEBAR -->

    <main>
        <h1>Your Inventory</h1>
        <div class="search">
            <i class="bi bi-search"></i>
            <input type="text">
            <button class="btn-search">Search</button>
        </div>

        <div class="add-product-wrapper">
            <a href="admin_add_product.php" class="add-product-button">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        </div>

        <!-- Product Table -->
        <div class="product-table">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Product ID</th>
                        <th>Product Title</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM products";
                $result = mysqli_query($con, $query);

                while ($row = mysqli_fetch_assoc($result)):
                    $statusClass = ($row['product_quantity'] <= 5) ? 'warning' : '';
                ?>
                    <tr>
                        <td><img class="product-image" src="pictures/default.png" alt="Product Image"></td>
                        <td><?= $row['product_ID']; ?></td>
                        <td><?= htmlspecialchars($row['product_title']); ?></td>
                        <td>₱ <?= number_format($row['product_price'], 2); ?></td>
                        <td><?= $row['product_quantity']; ?></td>
                        <td class="<?= $statusClass; ?>"><?= ucfirst($row['product_status']); ?></td>
                        <td class="actions">
                            <a href="admin_edit_product.php?id=<?= $row['product_ID']; ?>" class="action-link edit">Manage</a>
                            <a href="admin_delete_product.php?id=<?= $row['product_ID']; ?>" class="action-link delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php if ($row['product_status'] == 'active'): ?>
                                <a href="admin_deactivate_product.php?id=<?= $row['product_ID']; ?>" class="action-link deactivate">Deactivate</a>
                            <?php else: ?>
                                <a href="admin_activate_product.php?id=<?= $row['product_ID']; ?>" class="action-link activate">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
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
                <i class="bi bi-box-arrow-in-right"></i>
                <h3>Log Out</h3>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Highlight active sidebar link
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.sidebar a').forEach(link => {
    const linkPage = link.getAttribute('href').split('/').pop();
    link.classList.toggle('active', linkPage === currentPage);
});

// Sidebar toggle
document.querySelector("#menu-btn").addEventListener('click', () => {
    document.querySelector("aside").style.display = 'block';
});
document.querySelector("#close-btn").addEventListener('click', () => {
    document.querySelector("aside").style.display = 'none';
});

// Theme toggle
document.querySelector(".theme-toggler").addEventListener('click', () => {
    document.body.classList.toggle('dark-theme-variables');
    const toggler = document.querySelector(".theme-toggler");
    toggler.querySelector('i:nth-child(1)').classList.toggle('active');
    toggler.querySelector('i:nth-child(2)').classList.toggle('active');
});
</script>

</body>
</html>