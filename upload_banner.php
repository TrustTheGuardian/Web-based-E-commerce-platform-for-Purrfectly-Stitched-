<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_image'])) {
    $target_dir = "uploads/banners/";
    $file_name = basename($_FILES["banner_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO banners (image_path) VALUES ('$target_file')";
        mysqli_query($con, $sql);
        header('Location: admin_content.php?msg=Banner+Uploaded');
    } else {
        echo "Upload failed.";
    }
}
?>