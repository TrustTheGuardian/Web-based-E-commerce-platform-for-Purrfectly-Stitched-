<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to leave a review.";
    exit;
}

$user_id = $_SESSION['user_id'];
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = trim($_POST['review']);
$product_id = intval($_POST['product_id']);

if ($rating < 1 || $rating > 5 || empty($review)) {
    echo "Invalid input.";
    exit;
}

$stmt = $con->prepare("INSERT INTO reviews (product_ID, user_ID, rating, review_text) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $product_id, $user_id, $rating, $review);

if ($stmt->execute()) {
    echo "Review submitted!";
} else {
    echo "Error submitting review.";
}
?>