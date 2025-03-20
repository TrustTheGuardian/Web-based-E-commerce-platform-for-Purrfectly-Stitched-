<?php
include 'inventory_db.php'; // Database connection

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id'])) {  
    $product_id = intval($_GET['id']); 

    // Fetch the product's image path
    $query = "SELECT image FROM products WHERE product_id = $product_id"; 
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error fetching product: " . mysqli_error($conn)); // Debugging
    }

    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        $image_path = $product['image'];

        // Delete image file from server if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }

        // Delete product from database
        $deleteQuery = "DELETE FROM products WHERE product_id = $product_id"; 

        if (mysqli_query($conn, $deleteQuery)) {
            header("Location: admin_inventory.php?success=Product+Deleted+Successfully");
            exit();
        } else {
            die("Error deleting product: " . mysqli_error($conn)); // Debugging
        }
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid request: No product ID provided.");
}
?>
