<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);

    // Prevent empty insert
    if ($category_name !== "") {
        $stmt = $con->prepare("INSERT INTO product_category (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'category_id' => $stmt->insert_id
            ]);
        } else {
            echo json_encode(['success' => false]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false]);
    }
}
?>