<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = intval($_POST['review_id']);

    $stmt = $con->prepare("DELETE FROM reviews WHERE review_ID = ?");
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>