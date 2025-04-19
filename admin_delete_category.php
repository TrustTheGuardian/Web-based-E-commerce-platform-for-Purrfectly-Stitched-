<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);

    // Check if any products are using this category
    $checkQuery = "SELECT COUNT(*) AS count FROM products WHERE product_category_ID = $category_id";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        echo "in_use";
    } else {
        $deleteQuery = "DELETE FROM product_category WHERE product_category_ID = $category_id";
        if (mysqli_query($con, $deleteQuery)) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
?>