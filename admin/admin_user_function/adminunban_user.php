<?php
include 'db_connection.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE users SET status = 'active' WHERE id = $id";
    if(mysqli_query($con, $sql)) {
        header("Location: admin_users.php");
    } else {
        echo "Error unbanning user.";
    }
}
?>