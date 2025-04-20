<?php
session_start();
include 'db_connection.php';   // Assuming this is where you connect to your database

// Make sure product ID and quantity are provided
if (!isset($_POST['product_ID'], $_POST['quantity'])) {
    exit('Missing data.');
}

$product_ID = $_POST['product_ID'];
$quantity = intval($_POST['quantity']); // Ensure quantity is an integer
$user_ID = $_SESSION['user_id']; // Get the logged-in user ID from session

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

// 1. Check if user has an active cart in the database
$stmt = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();

// 2. If the user doesn't have a cart, create one
if ($result->num_rows === 0) {
    $stmt = $con->prepare("INSERT INTO cart (user_ID) VALUES (?)");
    $stmt->bind_param("i", $user_ID);
    $stmt->execute();
    $cart_ID = $stmt->insert_id; // Get the newly created cart ID
} else {
    // If the user already has a cart, get the existing cart_ID
    $cart = $result->fetch_assoc();
    $cart_ID = $cart['cart_ID'];
}

// 3. Check if the product already exists in the user's cart in the database
$stmt = $con->prepare("SELECT user_cart_ID FROM user_cart WHERE cart_ID = ? AND product_ID = ?");
$stmt->bind_param("ii", $cart_ID, $product_ID);
$stmt->execute();
$result = $stmt->get_result();

// 4. If the product is already in the cart, update the quantity
if ($result->num_rows > 0) {
    $stmt = $con->prepare("UPDATE user_cart SET quantity = quantity + ? WHERE cart_ID = ? AND product_ID = ?");
    $stmt->bind_param("iii", $quantity, $cart_ID, $product_ID);
    $stmt->execute();
} else {
    // Otherwise, add a new entry for this product in the user's cart
    $stmt = $con->prepare("INSERT INTO user_cart (cart_ID, product_ID, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $cart_ID, $product_ID, $quantity);
    $stmt->execute();
}

// Optionally, return the total number of items in the cart or the cart size
echo count($_SESSION['cart']); // You can also echo the total quantity in the cart
exit();
?>