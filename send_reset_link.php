<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Check if email exists
    $stmt = $con->prepare("SELECT user_ID FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store token
        $stmt = $con->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        // Send email
        $reset_link = "https://localhost/E-COMMERCE_PROJECT_PURRFECTLY_STITCHED/reset_password.php?token=$token";
        $subject = "Reset Your Password";
        $message = "Click here to reset your password: $reset_link";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "A reset link has been sent to your email.";
        } else {
            echo "Email sending failed.";
        }
    } else {
        echo "No user found with that email.";
    }
}
?>