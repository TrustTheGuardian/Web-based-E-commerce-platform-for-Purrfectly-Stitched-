<?php
session_start();
include 'db_connection.php';  // Connect to your database

// Make sure product ID and quantity are provided
if (!isset($_POST['product_ID'], $_POST['quantity'])) {
    exit('Missing data.');
}

$product_ID = intval($_POST['product_ID']);
$quantity = intval($_POST['quantity']); // Ensure quantity is an integer
$user_ID = $_SESSION['user_id']; // Get the logged-in user ID from session

// 1. Check the stock of the product
$stmt = $con->prepare("SELECT product_quantity FROM products WHERE product_ID = ?");
$stmt->bind_param("i", $product_ID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// If the product doesn't exist or the stock is not enough, exit
if (!$product || $product['product_quantity'] < $quantity) {
    echo "out_of_stock"; // Return an appropriate message if out of stock
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If the product already exists in the session cart, increment the quantity
if (isset($_SESSION['cart'][$product_ID])) {
    $_SESSION['cart'][$product_ID] += $quantity;
} else {
    // Otherwise, add the product to the session cart
    $_SESSION['cart'][$product_ID] = $quantity;
}

// 2. Check if user has an active cart in the database
$stmt = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ? AND status = 'active' LIMIT 1");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();

// 3. If the user doesn't have a cart, create one
if ($result->num_rows === 0) {
    $stmt = $con->prepare("INSERT INTO cart (user_ID, created_at, status) VALUES (?, NOW(), 'active')");
    $stmt->bind_param("i", $user_ID);
    $stmt->execute();
    $cart_ID = $stmt->insert_id; // Get the newly created cart ID
} else {
    // If the user already has a cart, get the existing cart_ID
    $cart = $result->fetch_assoc();
    $cart_ID = $cart['cart_ID'];
}

// 4. Check if the product already exists in the user's cart in the database
$stmt = $con->prepare("SELECT cart_item_ID FROM cart_items WHERE cart_ID = ? AND product_ID = ?");
$stmt->bind_param("ii", $cart_ID, $product_ID);
$stmt->execute();
$result = $stmt->get_result();

// 5. If the product is already in the cart, update the quantity
if ($result->num_rows > 0) {
    $stmt = $con->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_ID = ? AND product_ID = ?");
    $stmt->bind_param("iii", $quantity, $cart_ID, $product_ID);
    $stmt->execute();
} else {
    // Otherwise, add a new entry for this product in the cart_items table
    $stmt = $con->prepare("INSERT INTO cart_items (cart_ID, product_ID, quantity, added_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $cart_ID, $product_ID, $quantity);
    $stmt->execute();
}

// Optionally, return the total number of items in the cart
echo count($_SESSION['cart']); // Or total quantity if you prefer
exit();
?>