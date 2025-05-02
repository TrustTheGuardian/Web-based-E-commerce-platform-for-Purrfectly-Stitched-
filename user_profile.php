<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id']; // Get the user ID from the session

// Fetch user details
$query_user = "SELECT * FROM users WHERE user_ID = ?";
$stmt_user = $con->prepare($query_user);
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Fetch recent purchases for the logged-in user
$query_orders = "
    SELECT o.order_ID, o.ordered_at, o.total_price, o.order_status, p.product_title, oi.quantity, p.product_price
    FROM orders o
    JOIN order_items oi ON o.order_ID = oi.order_ID
    JOIN products p ON oi.product_ID = p.product_ID
    WHERE o.user_ID = ?
    ORDER BY o.ordered_at DESC
    LIMIT 5"; // Get the latest 5 orders
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<!-- header section starts -->
<header class="header col-12">
    <div class="logo">
        <a href="user_home.php"><img src="pictures/Purrfectly Stitch.png" alt="sample logo" width="60px" height = "50px">
        <h2>Purrfectly Stitched</h2></a>
    </div>

        <div class="menu">
        <a href="user_shop.php" class="d-none d-sm-block">Shop</a>
        
        <!-- Make profile image clickable to go to user_profile.php -->
        <a href="user_profile.php">
            <img src="<?php echo !empty($user['ProfileImage']) ? $user['ProfileImage'] : 'pictures/man-user-circle-icon.png'; ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
        </a>
        
        <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
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
<!-- header section ends -->

<!-- Profile Section -->


<section class="mt-5">
    <div class="container-fluid">
        <div class="row containerprofile">
            <div class="space col-1 d-none d-sm-block d-md-block"></div>
            <div class="profile col-12 col-lg-3">
                <!-- Display profile image dynamically -->
                <img src="<?php echo !empty($user['ProfileImage']) ? $user['ProfileImage'] : 'pictures/default-profile.png'; ?>" alt="" class="rounded-circle">
                
                <!-- Display user's full name -->
                <h4><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h4>
                
                <!-- Display gender dynamically -->
                <p class="text-muted mb-1" style="font-size: 14px;">
                    <span style="font-weight: bold;">Gender:</span> <?php echo htmlspecialchars($user['Gender']); ?>
                </p>

                <!-- Display email dynamically -->
                <p class="text-muted mb-1" style="font-size: 14px;">
                    <span style="font-weight: bold;">Email:</span> <?php echo htmlspecialchars($user['Email']); ?>
                </p>

                <!-- Display phone number dynamically -->
                <p class="text-muted mb-1" style="font-size: 14px;">
                    <span style="font-weight: bold;">Phone Num:</span> <?php echo htmlspecialchars($user['Mobile']); ?>
                </p>

                <!-- Display address dynamically -->
                <p class="text-muted mb-1" style="font-size: 14px;">
                    <span style="font-weight: bold;">Address:</span> <?php echo htmlspecialchars($user['Address']); ?>
                </p>
                <button onclick="window.location.href='user_editprofile.php'" class="btn btn1 ">⚙️Edit Profile</button>
                <button class="btn btn1" data-bs-toggle="modal" data-bs-target="#checkout-modal">🚪Log out</button>
            </div>

            <div class="purchases col-12 col-lg-7">
                <h3>Recent Purchases</h3>
                <?php
                if ($order_result && $order_result->num_rows > 0) {
                    while ($purchase = $order_result->fetch_assoc()) {
                        // Get the order details
                        $orderID = $purchase['order_ID'];
                        $orderDate = date("F j, Y", strtotime($purchase['ordered_at'])); // Format the date
                        $totalPrice = number_format($purchase['total_price'], 2); // Format the price
                        $orderStatus = $purchase['order_status'];
                        $productTitle = $purchase['product_title'];
                        $quantity = $purchase['quantity'];
                        $productPrice = number_format($purchase['product_price'], 2);
                        $totalItemPrice = number_format($productPrice * $quantity, 2); // Total price for the item
                        ?>
                        <div class="recentpurchaseproduct">
                            <img src="pictures/Purrfectly Stitch.png" alt=""> <!-- Use product image if available -->
                            <div class="productcontent">
                                <div class="productinfo">
                                    <h5 style="font-weight: bold;"><?php echo htmlspecialchars($productTitle); ?></h5>
                                    <p>Order #: <?php echo $orderID; ?></p>
                                    <p>Status: <span class="status <?php echo strtolower($orderStatus); ?>"><?php echo htmlspecialchars($orderStatus); ?></span></p>
                                </div>
                                <div class="productdateprice">
                                    <p><?php echo $orderDate; ?></p>
                                    <p style="font-weight: bold;">₱<?php echo $totalItemPrice; ?></p>
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
        <!-- Add a form that submits to logout.php -->
        <form action="logout.php" method="POST" id="logout-form">
          <button type="submit" class="btn btn-primary">Log out</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const logoutButton = document.querySelector('.confirm-btn');
  logoutButton.addEventListener('click', function() {
    document.getElementById('logout-form').submit(); // Submitting the form
  });
</script>

<script>
// This version just recalculates total on page load (based on existing HTML)
function updateTotal() {
        let total = 0;
        document.querySelectorAll("#cart-items .product-card").forEach(item => {
            const price = parseFloat(item.querySelector(".price").textContent.replace("₱", ""));
            const qty = parseInt(item.querySelector(".qty").textContent);
            total += price * qty;
        });
        const totalElement = document.getElementById("total-price");
        if (totalElement) {
            totalElement.textContent = `₱${total.toFixed(2)}`;
        }
    }

    // Call this on page load
    document.addEventListener("DOMContentLoaded", function () {
        updateTotal();
    });

    // You no longer need increaseQty, decreaseQty, or removeItem
    // because quantity changes are handled via <form> POSTs in PHP

    // If you still use a checkout modal and localStorage for orders:
    function populateCheckoutModal() {
        let total = 0;
        const orderItemsContainer = document.getElementById("order-items");
        const cart = JSON.parse(localStorage.getItem("cart")) || []; // Optional: replace with real data if needed

        orderItemsContainer.innerHTML = '';

        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            total += subtotal;

            const itemRow = document.createElement("div");
            itemRow.classList.add("item-row");
            itemRow.innerHTML = `
              <span>${item.title} (x${item.qty})</span>
              <span>₱${subtotal.toFixed(2)}</span>
            `;
            orderItemsContainer.appendChild(itemRow);
        });

        const totalContainer = document.getElementById("order-total");
        if (totalContainer) {
            totalContainer.textContent = `Total: ₱${total.toFixed(2)}`;
        }
    }

      function populateCheckoutModal() {
    let total = 0;
    const orderItemsContainer = document.getElementById("order-items");
    const cart = JSON.parse(localStorage.getItem("cart")) || []; // Replace with your session/cart data source

    // Clear any previous order items
    orderItemsContainer.innerHTML = '';

    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        total += subtotal;

        // Create an order item row dynamically
        const itemRow = document.createElement("div");
        itemRow.classList.add("item-row");
        itemRow.innerHTML = `
          <span>${item.title} (x${item.qty})</span>
          <span>₱${subtotal.toFixed(2)}</span>
        `;
        orderItemsContainer.appendChild(itemRow);
    });

    // Display total price
    const totalContainer = document.getElementById("order-total");
    totalContainer.textContent = `Total: ₱${total.toFixed(2)}`;
}
</script>
    <script src=".vscode/jsforhome.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
