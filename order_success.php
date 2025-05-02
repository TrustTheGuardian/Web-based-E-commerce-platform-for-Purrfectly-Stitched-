<?php
session_start();
require_once 'db_connection.php';

// Corrected: use order_ID (capital I and D) to match URL
if (!isset($_GET['order_ID'])) {
    echo "Order ID not found.";
    exit();
}

$order_ID = $_GET['order_ID'];

// Fetch order info
$order_sql = "SELECT o.*, u.FirstName FROM orders o 
              JOIN users u ON o.user_ID = u.user_ID 
              WHERE o.order_ID = ?";
$order_stmt = $con->prepare($order_sql);
$order_stmt->bind_param("i", $order_ID);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch ordered products from order_items (not user_cart)
$item_sql = "SELECT oi.quantity, p.product_title, p.product_price 
             FROM order_items oi 
             JOIN products p ON oi.product_ID = p.product_ID 
             WHERE oi.order_ID = ?";
$item_stmt = $con->prepare($item_sql);
$item_stmt->bind_param("i", $order_ID);
$item_stmt->execute();
$items_result = $item_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <link rel="stylesheet" href="css_files/ordersuccess.css">
</head>

<body>
    <div class="ordersuccessdiv">
        <h1>Thank you, <?= htmlspecialchars($order['FirstName']) ?>!</h1>
        <p>Your order has been placed successfully.</p>

        <h2>Order Summary</h2>
        <table class="order-table">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
            <?php while ($row = $items_result->fetch_assoc()):
                $subtotal = $row['product_price'] * $row['quantity'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['product_title']) ?></td>
                <td>₱<?= number_format($row['product_price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>₱<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                <td><strong>₱<?= number_format($order['total_price'], 2) ?></strong></td>
            </tr>
        </table>

        <p><strong>Payment Method:</strong> <?= ucfirst(htmlspecialchars($order['payment_method'])) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['ordered_at']) ?></p>
        
        
        <button class = "custom-btn" onclick="window.location.href='user_home.php'">Continue Shopping</button>

    </div>
</body>
</html>