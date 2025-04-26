<?php
session_start();
include 'db_connection.php';

// 1. Validate URL parameters
if (!isset($_GET['id'], $_GET['action'])) {
    die('Error: Missing Product ID or Action.');
}

$product_ID = intval($_GET['id']);
$action = $_GET['action'];

// 2. Validate action: must be "activate" or "deactivate"
if ($action !== 'activate' && $action !== 'deactivate') {
    die('Error: Invalid action.');
}

// 3. Check if product exists
$product_check = $con->prepare("SELECT product_ID FROM products WHERE product_ID = ?");
$product_check->bind_param("i", $product_ID);
$product_check->execute();
$product_check->store_result();

if ($product_check->num_rows === 0) {
    die('Error: Product not found.');
}
$product_check->close();

// 4. Decide the new status based on action
$new_status = ($action === 'activate') ? 'active' : 'deactive';

// 5. Update product_status
$update_stmt = $con->prepare("UPDATE products SET product_status = ? WHERE product_ID = ?");
$update_stmt->bind_param("si", $new_status, $product_ID);

if ($update_stmt->execute()) {
    header('Location: admin_inventory_v2.php');
    exit;
} else {
    die('Error updating product status: ' . $update_stmt->error);
}
?>