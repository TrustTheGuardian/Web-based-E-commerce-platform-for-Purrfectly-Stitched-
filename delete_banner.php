<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['banner_id'])) {
    $banner_id = $_POST['banner_id'];

    // First, fetch the image path of the banner to delete it from the file system
    $sql = "SELECT image_path FROM banners WHERE banner_ID = $banner_id";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $image_path = $row['image_path'];

    // Delete the banner from the database
    $delete_sql = "DELETE FROM banners WHERE banner_ID = $banner_id";
    if (mysqli_query($con, $delete_sql)) {
        // After successful deletion, remove the image file
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the file
        }
        header('Location: admin_content.php?msg=Banner+Deleted');
    } else {
        echo "Error deleting banner.";
    }
}
?>