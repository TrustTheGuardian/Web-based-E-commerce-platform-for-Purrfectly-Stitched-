<?php
include 'inventory_db.php'; // Include the database connection

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];  // Get the product ID from the URL

    // Get the image URL from the product_images table
    $query = "SELECT image_url FROM product_images WHERE product_id = $product_id LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $product = mysqli_fetch_assoc($result);
        $image_path = $product['image_url'];

        // If an image exists, delete it from the server
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);  // Delete the image file
        }

        // Delete the images from the product_images table
        $deleteImagesQuery = "DELETE FROM product_images WHERE product_id = $product_id";
        mysqli_query($conn, $deleteImagesQuery);

        // delete the product from the products table
        $deleteProductQuery = "DELETE FROM products WHERE product_id = $product_id";
        if (mysqli_query($conn, $deleteProductQuery)) {
            // Redirect to inventory page after successful deletion
            header("Location: admin_inventory.php?success=Product+Deleted+Successfully");
            exit();
        } else {
            echo "Error deleting product.";
        }
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid request.";
}
?>
