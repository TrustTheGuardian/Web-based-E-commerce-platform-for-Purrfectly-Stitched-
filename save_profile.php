<?php
session_start();
include 'db_connection.php';

// Check if the session user ID exists
if (!isset($_SESSION['user_id'])) {
    echo "No user ID in session.";
    exit;
}

$userID = $_SESSION['user_id'];

// Handle full name split
$fullName = trim($_POST['fullName']);
$nameParts = explode(' ', $fullName, 2);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';

$gender = $_POST['gender'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Handle profile image upload
$imagePath = null;
if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
    $imageName = basename($_FILES['profileImage']['name']);
    $imageName = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $imageName); // Sanitize file name
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileType = $_FILES['profileImage']['type'];

    if (in_array($fileType, $allowedTypes)) {
        $imageTmp = $_FILES['profileImage']['tmp_name'];
        $uploadDir = 'uploads/';
        $targetPath = $uploadDir . time() . '_' . $imageName;

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        // Move the file and check if it was successful
        if (move_uploaded_file($imageTmp, $targetPath)) {
            $imagePath = $targetPath;
        } else {
            // Log error for debugging
            echo "Error moving the uploaded file.";
            exit;
        }
    } else {
        // Log error for invalid file type
        echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        exit;
    }
}

// Prepare the update query
$query = "UPDATE users SET FirstName=?, LastName=?, Gender=?, Mobile=?, Address=?" . ($imagePath ? ", ProfileImage=?" : "") . " WHERE user_ID=?";
$stmt = $con->prepare($query);

// Bind parameters and execute the query
if ($imagePath) {
    $stmt->bind_param("ssssssi", $firstName, $lastName, $gender, $phone, $address, $imagePath, $userID);
} else {
    $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $phone, $address, $userID);
}

if ($stmt->execute()) {
    // Successfully updated, redirect
    header("Location: user_profile.php?status=updated");
    exit;
} else {
    // Log the error if the query fails
    echo "Error updating profile: " . $stmt->error;
    exit;
}
?>