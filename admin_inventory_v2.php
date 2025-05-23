<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?unauthorized=1");
    exit;
}
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
                        <!-- <th>Status</th> -->
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
                            <!-- <td class="<?= $statusClass; ?>"><?= ucfirst($row['product_status']); ?></td> -->
                            <td class="actions">
                                <a href="admin_edit_product.php?id=<?= $row['product_ID']; ?>" class="action-link edit">Manage</a>
                                <a href="#" class="action-link delete" data-id="<?= $row['product_ID']; ?>" onclick="openDeleteModal(<?= $row['product_ID']; ?>)">Delete</a>


                                <?php if ($row['product_status'] == 'active'): ?>
                                    <a href="admin_toggle_product.php?id=<?= $row['product_ID']; ?>&action=deactivate" class="action-link deactivate">Deactivate</a>
                                <?php else: ?>
                                    <a href="admin_toggle_product.php?id=<?= $row['product_ID']; ?>&action=activate" class="action-link activate">Activate</a>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <p>Are you sure you want to delete this product?</p>
            <div class="custom-modal-buttons">
                <button id="confirmDelete">Yes</button>
                <button id="cancelDelete">Cancel</button>
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


        // delete modal logic
        let selectedProductId = null; // to hold the product ID

        function openDeleteModal(productId) {
            selectedProductId = productId;
            document.getElementById('deleteModal').style.display = 'block';
        }

        document.getElementById('cancelDelete').addEventListener('click', () => {
            document.getElementById('deleteModal').style.display = 'none';
            selectedProductId = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', () => {
            if (selectedProductId !== null) {
                window.location.href = `admin_delete_product.php?id=${selectedProductId}`;
            }
        });

        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('deleteModal')) {
                document.getElementById('deleteModal').style.display = 'none';
                selectedProductId = null;
            }
        });

        // Close the remove modal if clicked outside
        window.addEventListener('click', (event) => {
            if (event.target === removeModal) {
                removeModal.style.display = 'none';
            }
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