<?php
session_start();

// Check if the cart exists in the session
if (isset($_SESSION['cart'])) {
    // Fetch cart data
    $cart = [];
    foreach ($_SESSION['cart'] as $product_ID => $quantity) {
        // Query product details from the database
        $sql = "SELECT product_title, product_price FROM products WHERE product_ID = '$product_ID'";
        $result = mysqli_query($con, $sql);
        $product = mysqli_fetch_assoc($result);

        // Add product data to cart
        $cart[] = [
            'product_title' => $product['product_title'],
            'product_price' => $product['product_price'],
            'quantity' => $quantity
        ];
    }

    // Return cart data as JSON
    echo json_encode($cart);
} else {
    // Return an empty cart if not available
    echo json_encode([]);
}
?>