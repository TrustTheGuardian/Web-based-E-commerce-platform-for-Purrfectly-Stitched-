<?php
include 'db_connection.php';

if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($con, $_GET['email']);
    $query = "SELECT * FROM users WHERE Email = '$email'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "Email is already taken.";
    } else {
        echo "";
    }
}

if (isset($_GET['mobile'])) {
    $mobile = mysqli_real_escape_string($con, $_GET['mobile']);
    $query = "SELECT * FROM users WHERE Mobile = '$mobile'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "Mobile number is already taken.";
    } else {
        echo "";
    }
}
?>