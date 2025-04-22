<?php
include 'db_connection.php'; // include your connection

if (isset($_GET['user_ID'])) {
    $userID = $_GET['user_ID'];

    // Check current ban status
    $checkQuery = "SELECT is_banned FROM users WHERE user_ID = ?";
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($isBanned);
    $stmt->fetch();
    $stmt->close();

    // Toggle ban status
    $newStatus = $isBanned ? 0 : 1;

    $updateQuery = "UPDATE users SET is_banned = ? WHERE user_ID = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("ii", $newStatus, $userID);

    if ($stmt->execute()) {
        header("Location: admin_users.php?message=User " . ($newStatus ? "banned" : "unbanned"));
    } else {
        echo "Error updating user.";
    }

    $stmt->close();
    $con->close();
}
?>
