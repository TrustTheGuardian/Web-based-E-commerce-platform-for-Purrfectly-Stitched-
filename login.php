<?php
session_start();
include 'db_connection.php'; // Make sure this sets up $con (the MySQLi connection)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize inputs
    $email    = mysqli_real_escape_string($con, $_POST['Email']);
    $password = $_POST['Password'];

    // 2. Prepare and execute user query (includes user_role)
    $stmt = $con->prepare("SELECT user_ID, Email, Password, is_banned, user_role FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // 🛑 Check if user is banned
        if ($user['is_banned'] == 1) {
            header("Location: index.php?banned=1");
            exit;
        }

        // 3. Verify password
        if (password_verify($password, $user['Password'])) {
            // ✅ Store session data
            $_SESSION['user_id']    = $user['user_ID'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role']  = $user['user_role'];

            // 4. Load existing cart (if any)
            $user_ID = $_SESSION['user_id'];
            $stmt_cart = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ?");
            $stmt_cart->bind_param("i", $user_ID);
            $stmt_cart->execute();
            $cart_result = $stmt_cart->get_result();

            if ($cart_result && $cart_result->num_rows === 1) {
                $cart = $cart_result->fetch_assoc();
                $cart_ID = $cart['cart_ID'];

                $stmt_items = $con->prepare("SELECT product_ID, quantity FROM cart_items WHERE cart_ID = ?");
                $stmt_items->bind_param("i", $cart_ID);
                $stmt_items->execute();
                $items_result = $stmt_items->get_result();

                $_SESSION['cart'] = [];
                while ($row = $items_result->fetch_assoc()) {
                    $_SESSION['cart'][$row['product_ID']] = $row['quantity'];
                }
            }

            // 5. Redirect based on user role
            if ($user['user_role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_home.php');
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
        exit;
    }
}

// If accessed via GET, redirect to homepage
header('Location: index.php');
exit;
?>