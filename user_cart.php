<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: index.php');
    exit;
}
include 'db_connection.php';

$userID = $_SESSION['user_id'];

// Fetch user profile image
$query = "SELECT ProfileImage FROM users WHERE user_ID = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$profileImage = 'pictures/default-profile.png';
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!empty($user['ProfileImage'])) {
        $profileImage = $user['ProfileImage'];
    }
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_ID'])) {
    $product_ID = $_POST['product_ID'];
    $action = $_POST['action'];

    // Fetch product stock
    $stmt = $con->prepare("SELECT product_quantity FROM products WHERE product_ID = ?");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $productResult = $stmt->get_result();

    if ($productResult && $productResult->num_rows > 0 && isset($_SESSION['cart'][$product_ID])) {
        $product = $productResult->fetch_assoc();
        $max_qty = $product['product_quantity'];
        $current_qty = $_SESSION['cart'][$product_ID];

        if ($action === 'increase' && $current_qty < $max_qty) {
            $_SESSION['cart'][$product_ID]++;
        } elseif ($action === 'decrease' && $current_qty > 1) {
            $_SESSION['cart'][$product_ID]--;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart</title>
  <link rel="stylesheet" href="css_files/userhome_styles.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

  <!-- HEADER -->
  <header class="header col-12">
        <div class="logo">
            <a href="index.php"><img src="pictures/Purrfectly Stitch.png" alt="sample logo" width="60px" height = "50px">
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

  <!-- CART CONTAINER -->
  <div class="contain mt-5">
    <div style="width: 80%;">
      <h2 class="homeproduct">Your Cart</h2>

      <div id="cart-items">
  <!-- Dynamic Cart Items -->
  <?php
            $total = 0.00;

            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_ID => $qty) {
                    $stmt = $con->prepare("SELECT p.product_title, p.product_price, p.product_quantity, pi.image_path 
                                           FROM products p 
                                           LEFT JOIN product_images pi ON p.product_ID = pi.product_ID 
                                           WHERE p.product_ID = ? LIMIT 1");
                    $stmt->bind_param("i", $product_ID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $title = $row['product_title'];
                        $price = $row['product_price'];
                        $stock = $row['product_quantity'];
                        $img = $row['image_path'];
                        $subtotal = $price * $qty;
                        $total += $subtotal;

                        echo "
                        <div class='product-card2' style='display: flex; align-items: center; border-bottom: 1px solid #ccc; padding: 20px 20px;'>
                            <img class='imgproduct' src='$img' alt='Product' style='border-radius: 10px; margin-right: 20px; width: 100px; height: 100px; object-fit: cover;'>
                            <div style='flex: 2;'>
                                <h3 class='card-title'>$title</h3>
                                <p class='price'>₱$price</p>
                            </div>
                            <div style='display: flex; align-items: center; gap: 10px;'>
                                <form method='POST' action='user_cart.php'>
                                    <input type='hidden' name='product_ID' value='$product_ID'>
                                    <input type='hidden' name='action' value='decrease'>
                                    <button class='btn-custom' ". ($qty <= 1 ? "disabled" : "") .">-</button>
                                </form>
                                <span class='qty'>$qty</span>
                                <form method='POST' action='user_cart.php'>
                                    <input type='hidden' name='product_ID' value='$product_ID'>
                                    <input type='hidden' name='action' value='increase'>
                                    <button class='btn-custom' ". ($qty >= $stock ? "disabled" : "") .">+</button>
                                </form>
                            </div>
                            <form method='POST' action='user_update_cart.php' style='margin-left: 20px;'>
                                <input type='hidden' name='product_ID' value='$product_ID'>
                                <input type='hidden' name='action' value='remove'>
                                <button class='custom-btn'>Remove</button>
                            </form>
                        </div>";
                    }
                }

                echo "<div style='text-align: right; margin-top: 30px;'>
                        <h3>Total: ₱" . number_format($total, 2) . "</h3>
                        <button class='btn-custom' data-bs-toggle='modal' data-bs-target='#checkout-modal' id='checkout-button'>Proceed to Checkout</button>
                      </div>";
            } else {
                echo "<p class='text-muted'>Your cart is empty.</p>";
            }
            ?>
    </div>

      <!-- Total Section -->
      <!-- <div style="text-align: right; margin-top: 30px;">
        <h3>Total: <span id="total-price">₱0</span></h3>
        <button class="btn-custom" data-bs-toggle="modal" data-bs-target="#checkout-modal" id="checkout-button">Proceed to Checkout</button>
      </div> -->
    </div>
  </div>


  <?php
    $cart_items = [];
    $cart_total = 0;

      if (!empty($_SESSION['cart'])) {
        $cart_ids = implode(",", array_keys($_SESSION['cart']));
        $sql = "SELECT * FROM products WHERE product_ID IN ($cart_ids)";
        $result = mysqli_query($con, $sql);
    
        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['product_ID'];
            $quantity = $_SESSION['cart'][$product_id];
            $product_total = $row['product_price'] * $quantity;
    
            $cart_items[] = [
                'product_title' => $row['product_title'],
                'product_price' => $row['product_price'],
                'quantity' => $quantity,
                'subtotal' => $product_total
            ];
    
            $cart_total += $product_total;
        }
    } 
    ?>

<div class="modal fade" id="checkout-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title">Checkout Confirmation</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="summary-container">
          <h2>Order Summary</h2>
          <div id="order-items">
            <?php foreach ($cart_items as $item): ?>
              <p>
                <strong><?= htmlspecialchars($item['product_title']) ?></strong>
                x <?= $item['quantity'] ?>
                — ₱<?= number_format($item['subtotal'],2) ?>
              </p>
            <?php endforeach; ?>
          </div>
          <div id="order-total">
            <strong>Total: ₱<?= number_format($cart_total,2) ?></strong>
          </div>
        </div>

        <!-- start the form here -->
        <form id="delivery-form" action="process_order.php" method="POST">
          <h5>Select Payment Method:</h5>
          <select name="payment_method" required>
              <option value="" disabled selected>Select an option</option>
              <option value="Online Payment">Online Payment</option>
              <option value="Cash on meet-up">Cash on meet-up</option>
          </select>

          <!-- pass the cart total -->
          <input type="hidden" name="cart_total" value="<?= $cart_total ?>">

          <!-- the footer and submit button must be inside the form -->
          <div class="modal-footer">
            <!-- this will post straight to process_order.php -->
            <button type="submit" class="btn confirm-btn">Confirm</button>
          </div>
        </form>
        <!-- end form -->
      </div>
    </div>
  </div>
</div>


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

function confirmCheckout() {
    const paymentMethod = document.querySelector('select[name="payment_method"]').value;

    if (!paymentMethod) {
        alert("Please select a payment method!");
        return;
    }

    // Handle checkout confirmation logic here (e.g., save the order in the database)

    // Example action: clear the cart and redirect to a confirmation page
    localStorage.removeItem("cart"); // Clear the cart (or use $_SESSION in PHP)
    alert(`Order confirmed! Payment method: ${paymentMethod}`);
    window.location.href = "order_confirmation.php"; // Redirect to order confirmation page
}

// Populate the modal with cart data when it's shown
$('#checkout-modal').on('shown.bs.modal', function () {
    populateCheckoutModal();
});


  </script>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
