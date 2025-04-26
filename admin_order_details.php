<?php
session_start();
require_once 'db_connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Fetch order info
    $modal_sql = "SELECT o.order_ID, o.payment_method, o.order_status, u.FirstName, u.LastName
                  FROM orders o
                  JOIN users u ON o.user_ID = u.user_ID
                  WHERE o.order_ID = ?";
    $modal_stmt = $con->prepare($modal_sql);
    $modal_stmt->bind_param("i", $order_id);
    $modal_stmt->execute();
    $modal_result = $modal_stmt->get_result();
    $order_info = $modal_result->fetch_assoc();

    if ($order_info):
?>
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order_info['order_ID']) ?></p>
        <p><strong>Customer name:</strong> <?= htmlspecialchars($order_info['FirstName'] . ' ' . $order_info['LastName']) ?></p>

        <p><strong>Products Ordered:</strong>
            <?php
            // Fetch ordered products
            $product_sql = "SELECT p.product_title, oi.quantity 
                            FROM order_items oi 
                            JOIN products p ON oi.product_ID = p.product_ID
                            WHERE oi.order_ID = ?";
            $product_stmt = $con->prepare($product_sql);
            $product_stmt->bind_param("i", $order_id);
            $product_stmt->execute();
            $product_result = $product_stmt->get_result();

            while ($prod = $product_result->fetch_assoc()) {
                echo htmlspecialchars($prod['product_title']) . " (x" . $prod['quantity'] . "), ";
            }
            $product_stmt->close();
            ?>
        </p>

        <p><strong>Payment:</strong> <?= ucfirst(htmlspecialchars($order_info['payment_method'])) ?></p>

        <p class="change-status"><strong>Change Order Status:</strong></p>
        <form action="admin_update_order_status.php" method="POST">
            <input type="hidden" name="order_ID" value="<?= $order_info['order_ID'] ?>">
            <select name="order_status" id="statusSelect">
                <option value="Pending" <?= $order_info['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Completed" <?= $order_info['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $order_info['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" id="saveStatus">Save</button>
        </form>
<?php
    else:
        echo "<p>Order not found.</p>";
    endif;
} else {
    echo "<p>No order selected.</p>";
}
?>