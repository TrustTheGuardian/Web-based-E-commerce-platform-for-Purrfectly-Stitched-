<?php
session_start();
include 'db_connection.php';

// 1) Ensure user is logged in
if (empty($_SESSION['user_id'])) { // should be user_id, not user_ID in session
    header('Location: index.php');
    exit;
}

// 2) Pull the user ID from the session
$user_ID = $_SESSION['user_id'];

// 3) Grab the payment method and cart total
if (!isset($_POST['payment_method'], $_POST['cart_total'])) {
    die('Invalid checkout submission.');
}
$payment_method = $_POST['payment_method'];
$cart_total     = $_POST['cart_total'];

// 4) Double-check that this user actually exists
$stmt = $con->prepare("SELECT 1 FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    die("Error: Logged-in user not found in database.");
}
$stmt->close();

// 5) Insert into orders
$order_status = 'Pending';
$ordered_at   = date('Y-m-d H:i:s');

$order_sql = "INSERT INTO orders (user_ID, total_price, payment_method, order_status, ordered_at)
              VALUES (?, ?, ?, ?, ?)";
$order_stmt = $con->prepare($order_sql);
$order_stmt->bind_param("idsss", $user_ID, $cart_total, $payment_method, $order_status, $ordered_at);

if (!$order_stmt->execute()) {
    die("DB Error (orders): " . $order_stmt->error);
}

$order_ID = $order_stmt->insert_id; // Now this is the correct order_ID
$order_stmt->close();

// 6) Insert each item into order_items
$item_sql = "INSERT INTO order_items (order_ID, product_ID, quantity, price) VALUES (?, ?, ?, ?)";
$item_stmt = $con->prepare($item_sql);

foreach ($_SESSION['cart'] as $product_ID => $qty) {
    // 1. Fetch the current price
    $price_stmt = $con->prepare("SELECT product_price FROM products WHERE product_ID = ?");
    $price_stmt->bind_param("i", $product_ID);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();

    if ($price_row = $price_result->fetch_assoc()) {
        $product_price = $price_row['product_price'];
    } else {
        $product_price = 0; // fallback
    }
    $price_stmt->close();

    // 2. Insert into order_items
    $item_stmt->bind_param("iiid", $order_ID, $product_ID, $qty, $product_price);
    if (!$item_stmt->execute()) {
        die("DB Error (order_items): " . $item_stmt->error);
    }

    // 3. Lessen the product stock
    $update_stock_stmt = $con->prepare("UPDATE products SET product_quantity = product_quantity - ? WHERE product_ID = ?");
    $update_stock_stmt->bind_param("ii", $qty, $product_ID);
    if (!$update_stock_stmt->execute()) {
        die("DB Error (updating stock): " . $update_stock_stmt->error);
    }
    $update_stock_stmt->close();
}
$item_stmt->close();

// 7) OPTIONAL: Clear the active cart in database
// You can delete cart_items and cart if you want to clean up after checkout
$con->query("DELETE FROM cart_items WHERE cart_ID IN (SELECT cart_ID FROM cart WHERE user_ID = $user_ID AND status = 'active')");
$con->query("DELETE FROM cart WHERE user_ID = $user_ID AND status = 'active'");

// 8) Clear session cart and redirect
unset($_SESSION['cart']);
header("Location: order_success.php?order_ID={$order_ID}");
exit;
?>