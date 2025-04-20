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
            <a href="admin_orders.html"><i class="bi bi-bag-check-fill"></i><h3>Orders</h3></a>
            <a href="admin_reports.html"><i class="bi bi-file-earmark-text-fill"></i><h3>Reports</h3></a>
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
                        <th>Product Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "
                        SELECT 
                            p.*, 
                            c.category_name 
                        FROM 
                            products p
                        JOIN 
                            product_category c 
                        ON 
                            p.product_category_ID = c.product_category_ID
                    ";
                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)):
                        $statusClass = ($row['product_quantity'] <= 5) ? 'warning' : '';

                        $product_ID = $row['product_ID'];
                        $img_sql = "SELECT image_path FROM product_images WHERE product_ID = $product_ID LIMIT 1";
                        $img_result = mysqli_query($con, $img_sql);

                        $imagePath = ($img_row = mysqli_fetch_assoc($img_result)) ? $img_row['image_path'] : 'pictures/default.png';
                    ?>
                        <tr>
                            <td><img class="product-image" src="<?= $imagePath ?>" alt="Product Image" width="80"></td>
                            <td><?= $row['product_ID']; ?></td>
                            <td><?= htmlspecialchars($row['product_title']); ?></td>
                            <td><?= htmlspecialchars($row['category_name']); ?></td>
                            <td>₱ <?= number_format($row['product_price'], 2); ?></td>
                            <td><?= $row['product_quantity']; ?></td>
                            <td class="<?= $statusClass; ?>"><?= ucfirst($row['product_status']); ?></td>
                            <td class="actions">
                                <a href="admin_edit_product.php?id=<?= $row['product_ID']; ?>" class="action-link edit">Manage</a>
                                <a href="#" class="action-link delete" data-id="<?= $row['product_ID']; ?>" onclick="openDeleteModal(<?= $row['product_ID']; ?>)">Delete</a>

                                <!-- Delete Confirmation Modal -->
                                <div id="deleteModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                                    background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
                                    <div style="background: white; padding: 20px; border-radius: 10px; width: 300px; text-align: center;">
                                        <p>Are you sure you want to delete this product?</p>
                                        <button onclick="confirmDelete()">Yes</button>
                                        <button onclick="closeDeleteModal()">Cancel</button>
                                    </div>
                                </div>

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

<script>
    let productIdToDelete = null;

    function openDeleteModal(productId) {
        productIdToDelete = productId;
        document.getElementById("deleteModal").style.display = "flex";
    }

    function closeDeleteModal() {
        document.getElementById("deleteModal").style.display = "none";
        productIdToDelete = null;
    }

    function confirmDelete() {
        if (productIdToDelete) {
            window.location.href = `admin_delete_product.php?id=${productIdToDelete}`;
        }
    }
</script>

</body>
</html>