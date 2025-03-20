<?php
include 'inventory_db.php'; // Database connection

// Fetch all products from database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Menu bar -->
    <?php include 'admin_menu.php'; ?>

    <!-- main display -->
    <section id="interface">
    <?php include 'admin_topbar.php'; ?>

    <h3 class="i-name">Inventory</h3>

    <div class="container mt-4">
        <h4>Add New Product</h4>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" class="form-control" placeholder="Description" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="category_id" class="form-control" placeholder="Category ID" required>
                </div>
                <div class="col-md-2 mt-2">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container mt-4">
        <h4>Product Inventory</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Category ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['category_id']; ?></td>
                        <td>
                            <img src="<?php echo $row['image']; ?>" width="80" height="80" alt="Product Image">
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 Purrfectly Stitch. All Rights Reserved.</p>
        </div>
    </footer>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
