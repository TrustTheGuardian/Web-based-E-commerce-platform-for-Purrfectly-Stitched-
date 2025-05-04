<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access.";
    exit;
}

$user_ID = $_SESSION['user_id'];
$original_password = $_POST['original_password'];
$new_password = $_POST['new_password'];

// Fetch current hashed password from DB
$query = "SELECT Password FROM users WHERE user_ID = '$user_ID'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if (!$row || !password_verify($original_password, $row['Password'])) {
    echo "<script>alert('Original password is incorrect.'); window.history.back();</script>";
    exit;
}

// Hash new password and update
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
$updateQuery = "UPDATE users SET Password = '$hashed_new_password' WHERE user_ID = '$user_ID'";
if (mysqli_query($con, $updateQuery)) {
    echo "<script>alert('Password updated successfully!'); window.location.href='user_profile.php';</script>";
} else {
    echo "<script>alert('Failed to update password.'); window.history.back();</script>";
}
?>