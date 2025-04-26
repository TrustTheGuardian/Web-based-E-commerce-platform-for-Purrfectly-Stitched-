<?php
session_start();
include 'db_connection.php';  // Connect to your database

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_ID = $_SESSION['user_id'];  // Get the user ID from the session

    // 1) Retrieve the user's cart_ID based on the user_ID from the 'cart' table
    $stmt = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param("i", $user_ID);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    // If cart exists, proceed to save the cart items
    if ($cart_result && $cart_result->num_rows === 1) {
        $cart = $cart_result->fetch_assoc();
        $cart_ID = $cart['cart_ID'];

        // 2) Save the current cart items from session to the cart_items table
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_ID => $quantity) {
                // Check if the product is already in the cart_items table
                $stmt_check = $con->prepare("SELECT cart_item_ID FROM cart_items WHERE cart_ID = ? AND product_ID = ?");
                $stmt_check->bind_param("ii", $cart_ID, $product_ID);
                $stmt_check->execute();
                $check_result = $stmt_check->get_result();

                if ($check_result && $check_result->num_rows > 0) {
                    // If the product already exists in the cart, update the quantity
                    $stmt_update = $con->prepare("UPDATE cart_items SET quantity = ? WHERE cart_ID = ? AND product_ID = ?");
                    $stmt_update->bind_param("iii", $quantity, $cart_ID, $product_ID);
                    $stmt_update->execute();
                } else {
                    // If the product doesn't exist, add it to the cart_items table
                    $stmt_insert = $con->prepare("INSERT INTO cart_items (cart_ID, product_ID, quantity, added_at) VALUES (?, ?, ?, NOW())");
                    $stmt_insert->bind_param("iii", $cart_ID, $product_ID, $quantity);
                    $stmt_insert->execute();
                }
            }
        }
    }
}

// Destroy session and log out
session_unset();
session_destroy();
header('Location: index.php');
exit;
?>