<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_id'])) {
    $id = intval($_POST['announcement_id']);
    $sql = "DELETE FROM announcements WHERE announcement_ID = $id";

    if (mysqli_query($con, $sql)) {
        header('Location: admin_content.php?msg=Announcement+Deleted');
        exit();
    } else {
        echo "Error deleting announcement: " . mysqli_error($con);
    }
}
?>