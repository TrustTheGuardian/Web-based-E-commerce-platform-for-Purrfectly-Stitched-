<?php
include 'db_connection.php';

if (isset($_POST['order_ID']) && isset($_POST['order_status'])) {
    $order_id = intval($_POST['order_ID']);
    $order_status = $_POST['order_status'];

    $update_sql = "UPDATE orders SET order_status = ? WHERE order_ID = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        header('Location: admin_orders.php?success=1');
        exit();
    } else {
        echo "Failed to update order status.";
    }
}
?>