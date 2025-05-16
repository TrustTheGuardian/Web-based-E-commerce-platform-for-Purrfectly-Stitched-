<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: index.php');
    exit;
}

$userID = $_SESSION['user_id'];

$query_user = "SELECT * FROM users WHERE user_ID = ?";
$stmt_user = $con->prepare($query_user);
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

$query_orders = "
    SELECT o.order_ID, o.ordered_at, o.total_price, o.order_status, p.product_title, oi.quantity, p.product_price
    FROM orders o
    JOIN order_items oi ON o.order_ID = oi.order_ID
    JOIN products p ON oi.product_ID = p.product_ID
    WHERE o.user_ID = ?
    ORDER BY o.ordered_at DESC
    LIMIT 5";
$stmt_orders = $con->prepare($query_orders);
$stmt_orders->bind_param("i", $userID);
$stmt_orders->execute();
$order_result = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css_files/userhome_styles.css">
    <link rel="stylesheet" href="css_files/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .purchases::-webkit-scrollbar {
            width: 8px;
        }
        .purchases::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .purchases::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .purchases::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body>
<header class="header col-12">
    <div class="logo">
        <a href="user_home.php">
            <img src="pictures/Purrfectly Stitch.png" alt="sample logo" width="60px" height="50px">
            <h2>Purrfectly Stitched</h2>
        </a>
    </div>

    <div class="menu">
        <a href="user_shop.php" class="d-none d-sm-block">Shop</a>
        <a href="user_profile.php">
            <img src="<?php echo !empty($user['ProfileImage']) ? $user['ProfileImage'] : 'pictures/man-user-circle-icon.png'; ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
        </a>
        <?php
        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
        ?>
        <a href="user_cart.php" class="position-relative">
            <i class="bi bi-cart"></i>
            <?php if ($cart_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $cart_count ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
</header>

<section class="mt-5">
    <div class="container-fluid">
        <div class="row containerprofile">
            <div class="space col-1 d-none d-sm-block d-md-block"></div>
            
            <div class="profileview col-12 col-lg-3">
                <img src="<?php echo !empty($user['ProfileImage']) ? $user['ProfileImage'] : 'pictures/default-profile.png'; ?>" alt="" class="rounded-circle">
                <h4><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h4>

                <div class="profile">
                    <p class="text-muted mb-1" style="font-size: 14px;">
                        <span style="font-weight: bold;">Gender:</span> <?php echo htmlspecialchars($user['Gender']); ?>
                    </p>
                    <p class="text-muted mb-1" style="font-size: 14px;">
                        <span style="font-weight: bold;">Email:</span> <?php echo htmlspecialchars($user['Email']); ?>
                    </p>
                    <p class="text-muted mb-1" style="font-size: 14px;">
                        <span style="font-weight: bold;">Phone Num:</span> <?php echo htmlspecialchars($user['Mobile']); ?>
                    </p>
                    <p class="text-muted mb-1" style="font-size: 14px;">
                        <span style="font-weight: bold;">Address:</span> <?php echo htmlspecialchars($user['Address']); ?>
                    </p>
                </div>
                <button onclick="window.location.href='user_editprofile.php'" class="btn btn1">⚙️Edit Profile</button>
                <button onclick="window.location.href='user_changepassword.php'" class="btn btn1">⚙️Change Password</button>
                <button class="btn btn1" data-bs-toggle="modal" data-bs-target="#checkout-modal">🚪Log out</button>
            </div>

            <!-- Purchases -->
            <div class="purchases col-12 col-lg-7" style="max-height: 700px; overflow-y: auto;">
                <h3>Recent Purchases</h3>
                <?php
                if ($order_result && $order_result->num_rows > 0) {
                    while ($purchase = $order_result->fetch_assoc()) {
                        $orderID = $purchase['order_ID'];
                        $orderDate = date("F j, Y", strtotime($purchase['ordered_at']));
                        $totalPrice = number_format($purchase['total_price'], 2);
                        $orderStatus = $purchase['order_status'];
                        $productTitle = $purchase['product_title'];
                        $quantity = $purchase['quantity'];
                        $productPrice = number_format($purchase['product_price'], 2);
                        $totalItemPrice = number_format($purchase['product_price'] * $quantity, 2);
                        ?>

                        
                        <div class="recentpurchaseproduct" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">
    <img src="pictures/Purrfectly Stitch.png" alt="" style="width: 80px; height: 80px; object-fit: contain; margin-right: 20px;">

    <!-- Content wrapper -->
    <div class="productcontent" style="display: flex; justify-content: space-between; flex-grow: 1;">
        
        <!-- LEFT SIDE -->
        <div style="flex-grow: 1;">
            <h5 style="font-weight: bold;"><?php echo htmlspecialchars($productTitle); ?></h5>
            <p>Order #: <?php echo $orderID; ?></p>
            <p>Status: 
                <span class="status <?php echo strtolower($orderStatus); ?>" style="font-weight: bold; color: <?php echo strtolower($orderStatus) === 'pending' ? 'orange' : (strtolower($orderStatus) === 'cancelled' ? 'red' : 'green'); ?>">
                    <?php echo htmlspecialchars($orderStatus); ?>
                </span>
            </p>
        </div>

        <!-- RIGHT SIDE -->
        <div style="display: flex; flex-direction: column; align-items: flex-end; min-width: 150px; margin-left: 20px;">
            <p><?php echo $orderDate; ?></p>
            <p style="font-weight: bold;">₱<?php echo $totalItemPrice; ?></p>

            <?php if (strtolower($orderStatus) === 'pending'): ?>
                <form method="POST" action="cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order?');" style="margin-top: 10px;">
                    <input type="hidden" name="order_ID" value="<?php echo $orderID; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

                    <?php
                    }
                } else {
                    echo "<p>No recent purchases found.</p>";
                }
                ?>
            </div>

            <div class="space col-1 d-none d-sm-block d-md-block"></div>
        </div>
    </div>
</section>

<!-- Logout Modal -->
<div class="modal fade" id="checkout-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Log out confirmation</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h1 style="font-size: 20px;">Are you sure you want to log out?</h1>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <form action="logout.php" method="POST" id="logout-form">
          <button type="submit" class="btn confirm-btn">Log out</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const logoutButton = document.querySelector('.confirm-btn');
logoutButton.addEventListener('click', function() {
    document.getElementById('logout-form').submit();
});
</script>

<script src=".vscode/jsforhome.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
