<?php
include 'inventory_db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE product_id=$id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}

// Handle real-time update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $category = isset($_POST['category_id']) && !empty($_POST['category_id']) ? "'".$_POST['category_id']."'" : "NULL";


    $query = "UPDATE products SET 
                product_name='$name', 
                description='$desc', 
                price='$price', 
                quantity='$qty', 
                category_id=$category 
            WHERE product_id='$id'";


    mysqli_query($conn, $query);

    // Handle image uploads
    if (isset($_FILES['product_images'])) {
        $images = $_FILES['product_images'];
        for ($i = 0; $i < count($images['name']); $i++) {
            $image_name = $images['name'][$i];
            $image_tmp = $images['tmp_name'][$i];
            $image_path = 'uploads/' . $image_name;
            move_uploaded_file($image_tmp, $image_path);

            // Insert image into product_images table
            $query = "INSERT INTO product_images (product_id, image_url) VALUES ('$id', '$image_path')";
            mysqli_query($conn, $query);
        }
    }

    header("Location: admin_inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    
    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">
    <link rel="stylesheet" href="css_files/inventory_styles.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Menu bar -->
    <?php include 'admin_menu.php'; ?>

    <!-- main display -->
    <section id="interface">
    <?php include 'admin_topbar.php'; ?>

    <h3 class="i-name">Edit Product</h3>

    <div class="container mt-4">
        <form action="edit_product.php?id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" class="form-control mb-2" required>
            <textarea name="description" class="form-control mb-2" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" class="form-control mb-2" required>
            <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" class="form-control mb-2" required>

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

            <!-- Image upload section -->
            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner" id="imagePreview">
                    <?php
                    $query = "SELECT * FROM product_images WHERE product_id = {$product['product_id']}";
                    $result = mysqli_query($conn, $query);
                    $active = true;
                    while ($image = mysqli_fetch_assoc($result)) {
                        $activeClass = $active ? 'active' : '';
                        echo "<div class='carousel-item $activeClass'>
                                <div class='position-relative'>
                                    <img src='" . htmlspecialchars($image['image_url']) . "' class='d-block w-100' alt='Product Image'>
                                    <button type='button' class='btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image-btn' data-image-id='" . htmlspecialchars($image['image_id']) . "'>&times;</button>
                                </div>
                            </div>";
                        $active = false;
                    }
                    ?>
                </div>
            </div>
            <!-- External Navigation Buttons -->
            <div class="d-flex justify-content-between mt-2">
                <button class="btn btn-secondary" id="prevImage" type="button">Previous</button>
                <button class="btn btn-secondary" id="nextImage" type="button">Next</button>
            </div>
                        
            <input type="file" name="product_images[]" multiple class="form-control mt-2" id="imageInput">
            <button type="button" id="addImageBtn" class="btn btn-primary d-block mt-2">Add Image</button>
            
            <!-- Image upload section END-->   
             
            <button type="submit" name="edit_product" class="btn btn-success w-100 d-block mt-2 mb-2">Save Changes</button>
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
