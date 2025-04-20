<?php
session_start();
include 'db_connection.php';

if (isset($_POST['product_ID']) && isset($_POST['action'])) {
    $product_ID = $_POST['product_ID'];
    $action = $_POST['action'];

    // Fetch product quantity from the database
    $sql = "SELECT product_quantity FROM products WHERE product_ID = '$product_ID' LIMIT 1";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $product_quantity = $row['product_quantity'];

    // Update cart based on action
    if ($action == 'increase') {
        if ($_SESSION['cart'][$product_ID] < $product_quantity) {
            $_SESSION['cart'][$product_ID]++;
        }
    } elseif ($action == 'decrease') {
        if ($_SESSION['cart'][$product_ID] > 1) {
            $_SESSION['cart'][$product_ID]--;
        }
    } elseif ($action == 'remove') {
        unset($_SESSION['cart'][$product_ID]);
    }

    // Redirect back to cart
    header("Location: user_cart.php");
    exit();
}
?>