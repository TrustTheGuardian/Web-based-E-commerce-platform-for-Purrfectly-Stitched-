<?php
include 'inventory_db.php'; // Database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch product data if ID is provided
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Ensure it's an integer for security

    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Error fetching product: " . mysqli_error($conn));
    }

    $product = mysqli_fetch_assoc($result);
    
    if (!$product) {
        die("Product not found.");
    }
}

// Update Product Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);

    $updateQuery = "UPDATE products SET 
                    product_name = '$product_name', 
                    description = '$description', 
                    price = '$price', 
                    quantity = '$quantity', 
                    category_id = '$category_id'";

    // Image Handling (if new image is uploaded)
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $updateQuery .= ", image = '$target_file'"; // Update image only if upload is successful
        } else {
            echo "Error uploading image.";
        }
    }

    $updateQuery .= " WHERE product_id = $product_id"; // Corrected WHERE clause

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: inventory.php?success=Product+Updated+Successfully");
        exit();
    } else {
        die("Error updating product: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="css_files/adminstyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>Edit Product</h3>
        <form action="edit_product.php?id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            
            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($product['description']); ?>" required>
            </div>

            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Category ID</label>
                <input type="number" name="category_id" class="form-control" value="<?php echo $product['category_id']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Current Image</label><br>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" width="100" height="100">
            </div>

            <div class="mb-3">
                <label>New Image (optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="inventory.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
