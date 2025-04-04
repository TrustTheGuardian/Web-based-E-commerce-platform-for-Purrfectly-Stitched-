<?php
include 'inventory_db.php';

// Handle real-time update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $category = $_POST['category_id'];
    
    // Insert product details into the products table
    $query = "INSERT INTO products (product_name, description, price, quantity, category_id) VALUES ('$name', '$desc', '$price', '$qty', '$category')";
    mysqli_query($conn, $query);
    $product_id = mysqli_insert_id($conn);

    // Handle image uploads
    if (isset($_FILES['product_images'])) {
        $images = $_FILES['product_images'];
        for ($i = 0; $i < count($images['name']); $i++) {
            $image_name = $images['name'][$i];
            $image_tmp = $images['tmp_name'][$i];
            $image_path = 'uploads/' . $image_name;
            move_uploaded_file($image_tmp, $image_path);

            // Insert image into product_images table
            $query = "INSERT INTO product_images (product_id, image_url) VALUES ('$product_id', '$image_path')";
            mysqli_query($conn, $query);
        }
    }

    header("Location: admin_inventory.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $new_category = $_POST['new_category'];
    
    if (!empty($new_category)) {
        $query = "INSERT INTO categories (category_id) VALUES ('$new_category')";
        mysqli_query($conn, $query);
        header("Location: add_product.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_category'])) {
    $category_to_delete = $_POST['delete_category'];
    
    if (!empty($category_to_delete)) {
        $query = "DELETE FROM categories WHERE category_id = '$category_to_delete'";
        mysqli_query($conn, $query);
        header("Location: add_product.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">
    <link rel="stylesheet" href="css_files/inventory_styles.css">

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

    <h3 class="i-name">Add a Product</h3>

    <div class="container mt-4">
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="product_name" required placeholder="Product Name" class="form-control mb-2">
        <textarea name="description" required placeholder="Description" class="form-control mb-2"></textarea>
        <input type="number" step="0.01" name="price" required placeholder="Price" class="form-control mb-2">
        <input type="number" name="quantity" required placeholder="Quantity" class="form-control mb-2">
        
        <!-- Category selection -->
        <div class="mb-2">
            <select name="category_id" class="form-control">
                <option value="">Select Category</option>
                <?php
                $categories = mysqli_query($conn, "SELECT * FROM categories");
                while ($cat = mysqli_fetch_assoc($categories)) {
                    echo "<option value='" . htmlspecialchars($cat['category_id']) . "'>" . htmlspecialchars($cat['category_id']) . "</option>";
                }
                ?>
            </select>
            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add New Category</button>
            <button type="button" class="btn btn-link p-0 text-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">Delete Category</button>
        </div>

        <!-- Image upload and carousel section -->
        <div class="mb-2">
            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner" id="imagePreview">
                    <div class="carousel-item active">
                        <i class="bi bi-file-earmark-image-fill"></i>
                    </div>
                </div>
            </div>

            <!-- External Navigation Buttons -->
            <div class="d-flex justify-content-between mt-2">
                <button class="btn btn-secondary" id="prevImage" type="button">Previous</button>
                <button class="btn btn-secondary" id="nextImage" type="button">Next</button>
            </div>

            <input type="file" name="product_images[]" multiple class="form-control mt-2" id="imageInput">
            <button type="button" id="addImageBtn" class="btn btn-primary mt-2">Add Image</button>
        </div>
        <!-- Image upload and carousel section END -->
         
        <button type="submit" name="add_product" class="btn btn-success w-100 d-block mb-2">Add Product</button>
        <a href="admin_inventory.php" class="btn btn-secondary d-block">Cancel</a>
    </form>
</div>

<!-- Modal for Adding Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_product.php" method="POST">
                    <input type="text" name="new_category" required placeholder="Category Name" class="form-control mb-2">
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deleting Category -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_product.php" method="POST">
                    <select name="delete_category" class="form-control mb-2">
                        <option value="">Select Category to Delete</option>
                        <?php
                        $categories = mysqli_query($conn, "SELECT * FROM categories");
                        while ($cat = mysqli_fetch_assoc($categories)) {
                            echo "<option value='" . htmlspecialchars($cat['category_id']) . "'>" . htmlspecialchars($cat['category_id']) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 Purrfectly Stitch. All Rights Reserved.</p>
        </div>
    </footer>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('addImageBtn').addEventListener('click', function () {
            let input = document.getElementById('imageInput');
            let files = input.files;
            if (files.length > 0) {
                let carouselInner = document.getElementById('imagePreview');

                for (let i = 0; i < files.length; i++) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let newItem = document.createElement('div');
                        newItem.classList.add('carousel-item');

                        if (carouselInner.querySelectorAll('.carousel-item').length === 1) {
                            newItem.classList.add('active');
                            carouselInner.querySelector('.carousel-item').classList.remove('active');
                        }

                        newItem.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="d-block w-100" alt="Product Image">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image-btn">&times;</button>
                            </div>
                        `;

                        carouselInner.appendChild(newItem);
                        updateNavigationVisibility(); // Update buttons visibility

                        // Delete Image Function
                        newItem.querySelector('.delete-image-btn').addEventListener('click', function () {
                            let item = this.closest('.carousel-item');

                            if (item.classList.contains('active')) {
                                let next = item.nextElementSibling || item.previousElementSibling;
                                if (next) next.classList.add('active');
                            }

                            item.remove();
                            updateNavigationVisibility(); // Update buttons visibility after deletion
                        });
                    };
                    reader.readAsDataURL(files[i]);
                }

                input.value = '';
            }
        });

        // External navigation
        document.getElementById('prevImage').addEventListener('click', function () {
            let activeItem = document.querySelector('.carousel-item.active');
            let prevItem = activeItem.previousElementSibling;

            if (prevItem) {
                activeItem.classList.remove('active');
                prevItem.classList.add('active');
            }
        });

        document.getElementById('nextImage').addEventListener('click', function () {
            let activeItem = document.querySelector('.carousel-item.active');
            let nextItem = activeItem.nextElementSibling;

            if (nextItem) {
                activeItem.classList.remove('active');
                nextItem.classList.add('active');
            }
        });

        // Function to show/hide navigation buttons
        function updateNavigationVisibility() {
            let totalItems = document.querySelectorAll('.carousel-item').length;
            let prevButton = document.getElementById('prevImage');
            let nextButton = document.getElementById('nextImage');

            if (totalItems > 1) {
                prevButton.style.display = 'block';
                nextButton.style.display = 'block';
            } else {
                prevButton.style.display = 'none';
                nextButton.style.display = 'none';
            }
        }

        // Hide navigation buttons on page load (initially no images)
        updateNavigationVisibility();

    </script>
</body>
</html>
