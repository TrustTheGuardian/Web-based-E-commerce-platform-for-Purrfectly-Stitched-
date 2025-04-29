<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $date = $_POST['announcement_date'];
    $content = mysqli_real_escape_string($con, $_POST['content']);

    $sql = "INSERT INTO announcements (title, content, announcement_date)
            VALUES ('$title', '$content', '$date')";

    if (mysqli_query($con, $sql)) {
        header('Location: admin_content.php?msg=Announcement+Posted');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>