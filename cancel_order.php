<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ID'])) {
    $userID = $_SESSION['user_id'];
    $orderID = intval($_POST['order_ID']);

    // Step 1: Verify the order belongs to the user and is pending
    $checkSql = "SELECT order_status FROM orders WHERE order_ID = ? AND user_ID = ?";
    $stmt = $con->prepare($checkSql);
    $stmt->bind_param("ii", $orderID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();

        if (strtolower($order['order_status']) === 'pending') {
            // Step 2: Update the order status to "Cancelled"
            $updateSql = "UPDATE orders SET order_status = 'Cancelled' WHERE order_ID = ?";
            $updateStmt = $con->prepare($updateSql);
            $updateStmt->bind_param("i", $orderID);

            if ($updateStmt->execute()) {
                $_SESSION['message'] = "Order #$orderID has been cancelled.";
            } else {
                $_SESSION['message'] = "Failed to cancel the order. Please try again.";
            }
        } else {
            $_SESSION['message'] = "This order cannot be cancelled.";
        }
    } else {
        $_SESSION['message'] = "Order not found or access denied.";
    }

    // Redirect back to purchases page
    header("Location: user_profile.php");
    exit;
} else {
    // Invalid request
    $_SESSION['message'] = "Invalid request.";
    header("Location: user_profile.php");
    exit;
}
?>