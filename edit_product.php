<?php
include 'inventory_db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE product_id=$id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $category = isset($_POST['category_id']) && !empty($_POST['category_id']) ? $_POST['category_id'] : 'NULL';

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
            <select name="category_id" class="form-control mb-2">
                <option value="">Select Category</option>
                <?php
                $categories = mysqli_query($conn, "SELECT * FROM categories");
                while ($cat = mysqli_fetch_assoc($categories)) {
                    $selected = ($cat['category_id'] == $product['category_id']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($cat['category_id']) . "' $selected>" . htmlspecialchars($cat['category_id']) . "</option>";
                }
                ?>
            </select>

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
                                <img src='" . htmlspecialchars($image['image_url']) . "' class='d-block w-100' alt='Product Image'>
                            </div>";
                        $active = false;
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <input type="file" name="product_images[]" multiple class="form-control mt-2" id="imageInput">
            <button type="button" id="addImageBtn" class="btn btn-primary mt-2">Add Image</button>

            <button type="submit" name="edit_product" class="btn btn-success d-block mb-2">Save Changes</button>
            <a href="admin_inventory.php" class="btn btn-secondary d-block">Cancel</a>
        </form>
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
        document.getElementById('addImageBtn').addEventListener('click', function() {
            let input = document.getElementById('imageInput');
            let files = input.files;
            if (files.length > 0) {
                let carouselInner = document.getElementById('imagePreview');
                for (let i = 0; i < files.length; i++) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let newItem = document.createElement('div');
                        newItem.classList.add('carousel-item');
                        if (i === 0) newItem.classList.add('active');
                        newItem.innerHTML = `<img src="${e.target.result}" class="d-block w-100" alt="Product Image">`;
                        carouselInner.appendChild(newItem);
                    };
                    reader.readAsDataURL(files[i]);
                }
            }
        });
    </script>
</body>
</html>
