<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['reply'])) {
    $review_id = intval($_POST['review_id']);
    $reply = trim($_POST['reply']);

    // Update the reply
    $stmt = $con->prepare("UPDATE reviews SET admin_reply = ? WHERE review_ID = ?");
    $stmt->bind_param("si", $reply, $review_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>