<?php
// Database credentials
$servername = "localhost";  // If running locally
$username = "root";         // Default XAMPP/MAMP/WAMP user
$password = "";             // Default is empty for local servers
$database = "inventory_db"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
