<?php
session_start();
include 'db_connection.php';  // Connect to your database

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_ID = $_SESSION['user_id'];  
    $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

    // Only save cart if NOT admin and cart session exists
    if (!$isAdmin && !empty($_SESSION['cart'])) {
        // Get the user's active cart
        $stmt = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ? AND status = 'active' LIMIT 1");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $cart_result = $stmt->get_result();

        if ($cart_result && $cart_result->num_rows === 1) {
            $cart = $cart_result->fetch_assoc();
            $cart_ID = $cart['cart_ID'];

            foreach ($_SESSION['cart'] as $product_ID => $quantity) {
                // Check if product already exists in cart_items
                $stmt_check = $con->prepare("SELECT cart_item_ID FROM cart_items WHERE cart_ID = ? AND product_ID = ?");
                $stmt_check->bind_param("ii", $cart_ID, $product_ID);
                $stmt_check->execute();
                $check_result = $stmt_check->get_result();

                if ($check_result && $check_result->num_rows > 0) {
                    // Update quantity
                    $stmt_update = $con->prepare("UPDATE cart_items SET quantity = ? WHERE cart_ID = ? AND product_ID = ?");
                    $stmt_update->bind_param("iii", $quantity, $cart_ID, $product_ID);
                    $stmt_update->execute();
                } else {
                    // Insert new item
                    $stmt_insert = $con->prepare("INSERT INTO cart_items (cart_ID, product_ID, quantity, added_at) VALUES (?, ?, ?, NOW())");
                    $stmt_insert->bind_param("iii", $cart_ID, $product_ID, $quantity);
                    $stmt_insert->execute();
                }
            }
        }
    }
}

// Clean up session
session_unset();
session_destroy();

// Redirect based on role
if (isset($isAdmin) && $isAdmin) {
    header("Location: index.php"); // Admin logout redirect
} else {
    header("Location: index.php"); // User logout redirect
}
exit;
?>