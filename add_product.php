<?php
include 'inventory_db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];

    // Ensure the uploads directory exists
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if an image was uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = basename($_FILES["image"]["product_name"]);
        $target_file = $target_dir . $image_name;
        $image_uploaded = move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $target_file = ""; // No image uploaded
        $image_uploaded = false;
    }

    // Insert product into database
    $query = "INSERT INTO products (product_name, description, price, quantity, category_id, image) 
              VALUES ('$product_name', '$description', '$price', '$quantity', '$category_id', '$target_file')";

    if (mysqli_query($conn, $query)) {
        header("Location: admin_inventory.php?success=Product Added Successfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
