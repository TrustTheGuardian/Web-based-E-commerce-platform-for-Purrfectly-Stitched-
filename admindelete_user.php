<?php
include 'db_connection.php';

if(isset($_GET['user_ID'])) {
    $id = $_GET['user_ID'];
    $sql = "DELETE FROM users WHERE user_ID = $id";
    if(mysqli_query($con, $sql)) {
        header("Location: admin_users.php");
    } else {
        echo "Error deleting user.";
    }
}
?>