<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $review_ID = intval($_POST['review_ID'] ?? 0);

    if ($action === 'reply' && isset($_POST['reply_text'])) {
        $reply_text = mysqli_real_escape_string($con, $_POST['reply_text']);
        $query = "UPDATE reviews SET admin_reply = '$reply_text' WHERE review_ID = $review_ID";
        mysqli_query($con, $query);
        echo "Reply saved.";
        exit;
    }

    if ($action === 'delete') {
        $query = "DELETE FROM reviews WHERE review_ID = $review_ID";
        mysqli_query($con, $query);
        echo "Review deleted.";
        exit;
    }
}
?>