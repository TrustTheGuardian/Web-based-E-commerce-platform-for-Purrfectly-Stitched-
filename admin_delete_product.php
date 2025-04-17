<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prevent SQL injection
    $id = mysqli_real_escape_string($con, $id);
    
    $sql = "DELETE FROM products WHERE product_ID = $id";
    if (mysqli_query($con, $sql)) {
        header("Location: admin_inventory_v2.php");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($con);
    }
}
?>