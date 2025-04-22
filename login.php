<?php
session_start();
include 'db_connection.php'; // Ensure this includes $con (your DB connection)

// Only handle POST from the modal:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Grab & sanitize
    $email    = mysqli_real_escape_string($con, $_POST['Email']);
    $password = $_POST['Password'];

    // 2) Lookup user
    $stmt = $con->prepare("SELECT user_ID, Email, Password, is_banned FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // 🛑 Check if banned
        if ($user['is_banned'] == 1) {
            header("Location: index.php?banned=1");
            exit;
        }

        // 3) Verify
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id']    = $user['user_ID'];
            $_SESSION['user_email'] = $user['Email'];

            // 4) Retrieve cart_ID for the logged-in user from the 'cart' table
            $user_ID = $_SESSION['user_id'];

            $stmt_cart = $con->prepare("SELECT cart_ID FROM cart WHERE user_ID = ?");
            $stmt_cart->bind_param("i", $user_ID);
            $stmt_cart->execute();
            $cart_result = $stmt_cart->get_result();

            if ($cart_result && $cart_result->num_rows === 1) {
                $cart = $cart_result->fetch_assoc();
                $cart_ID = $cart['cart_ID'];

                // 5) Retrieve items from the 'user_cart' table using the cart_ID
                $stmt_items = $con->prepare("SELECT product_ID, quantity FROM user_cart WHERE cart_ID = ?");
                $stmt_items->bind_param("i", $cart_ID);
                $stmt_items->execute();
                $items_result = $stmt_items->get_result();

                $_SESSION['cart'] = [];
                while ($row = $items_result->fetch_assoc()) {
                    $_SESSION['cart'][$row['product_ID']] = $row['quantity'];
                }
            }

            // Redirect to the user home page after login
            header('Location: user_home.php');
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

// If someone GETs this file, just bounce them back:
header('Location: index.php');
exit;
?>
