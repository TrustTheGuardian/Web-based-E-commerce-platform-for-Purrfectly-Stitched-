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
            <div class="mb-3">
                <input type="file" name="product_images[]" multiple class="form-control mb-2" id="imageInput">
                <div id="imagePreviewContainer" class="d-flex overflow-auto gap-2 mt-3">
                    <?php
                    $query = "SELECT * FROM product_images WHERE product_id = {$product['product_id']}";
                    $result = mysqli_query($conn, $query);
                    while ($image = mysqli_fetch_assoc($result)) {
                        echo "<div class='position-relative'>
                                <img src='" . htmlspecialchars($image['image_url']) . "' class='rounded' style='width: 120px; height: 120px; object-fit: cover;'>
                                <button type='button' class='btn btn-sm btn-danger position-absolute' style='top: 5px; right: 5px;' onclick='deleteImage(\"" . htmlspecialchars($image['image_url']) . "\")'>&times;</button>
                            </div>";
                    }
                    ?>
                </div>
            </div>

            <button type="submit" name="edit_product" class="btn btn-success w-100 d-block mb-2">Save Changes</button>
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
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('imagePreviewContainer');

        imageInput.addEventListener('change', () => {
            for (let i = 0; i < imageInput.files.length; i++) {
                const file = imageInput.files[i];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '120px';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';
                    img.className = 'rounded';

                    const deleteBtn = document.createElement('button');
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-sm btn-danger position-absolute';
                    deleteBtn.style.top = '5px';
                    deleteBtn.style.right = '5px';
                    deleteBtn.onclick = function () {
                        wrapper.remove();
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            }
        });

        function deleteImage(imageUrl) {
            alert('Image deleted: ' + imageUrl);
        }
    </script>
</body>
</html>
