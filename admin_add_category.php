<?php
include 'db_connection.php'; // assuming you use $con for MySQLi

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $category_name = mysqli_real_escape_string($con, $_POST['category_name']);

    // Check if category already exists
    $check = mysqli_query($con, "SELECT * FROM product_category WHERE category_name = '$category_name'");
    if (mysqli_num_rows($check) > 0) {
        echo "exists";
        exit;
    }

    $query = "INSERT INTO product_category (category_name) VALUES ('$category_name')";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($con);
    }
}
?>