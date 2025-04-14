<?php
$host = "localhost";      // or 127.0.0.1
$username = "root";       // your DB username
$password = "";           // your DB password
$dbname = "e-commerce";   // your database name

// Create a connection
$con = mysqli_connect($host, $username, $password, $dbname);

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>