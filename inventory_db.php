<?php
    // inventory_db.php - Database Connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "inventory_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
