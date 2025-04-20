<?php
session_start();
include 'db_connection.php'; 
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
            <div class="btn-group custom-dropdown">
              <button type="button" class="dropdown-toggle d-sm-block d-none" data-bs-toggle="dropdown">
                  Profile
              </button>
              <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#loginModal" data-bs-toggle="modal">Account</a></li>
                  <li><a class="dropdown-item" href="registration.php">To Pay</a></li>
                  <li><a class="dropdown-item" href="registration.php">To Receive</a></li>
              </ul>
              
            </div>
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
            <div class="btn-group custom-dropdown d-sm-none">
              <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown">
                  Menu
              </button>
              <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Home</a></li>
                  <li><a class="dropdown-item" href="#">Shop</a></li>
                  <li><a class="dropdown-item" href="#">Profile</a></li>
              </ul>
            </div>
            <a href="logout.php" class="btn btn-outline-secondary ms-3">Log Out</a>
        </div>
    </header>

  <!-- CART CONTAINER -->
  <div class="contain mt-5">
    <div style="width: 80%;">
      <h2 class="homeproduct">Your Cart</h2>

      <div id="cart-items">
  <!-- Dynamic Cart Items -->
  <?php
        $total = 0.00; // Initialize the total variable

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_ID => $qty) {
                // Update the query to include product_quantity
                $sql = "SELECT p.product_title, p.product_price, p.product_quantity, pi.image_path 
                        FROM products p 
                        LEFT JOIN product_images pi ON p.product_ID = pi.product_ID 
                        WHERE p.product_ID = '$product_ID' LIMIT 1";
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($result);

                $title = $row['product_title'];
                $price = $row['product_price'];
                $product_quantity = $row['product_quantity']; // Get product quantity
                $img = $row['image_path'];
                $subtotal = $price * $qty;
                $total += $subtotal;

                // Update quantity if needed
                if (isset($_POST['action']) && $_POST['product_ID'] == $product_ID) {
                    if ($_POST['action'] == 'increase' && $qty < $product_quantity) {
                        $_SESSION['cart'][$product_ID] = $qty + 1;
                    } elseif ($_POST['action'] == 'decrease' && $qty > 1) {
                        $_SESSION['cart'][$product_ID] = $qty - 1;
                    }
                }

                echo "
                <div class='product-card' style='display: flex; align-items: center; border-bottom: 1px solid #ccc; padding: 20px 20px;'>
                    <img class='imgproduct' src='$img' alt='Product' style='border-radius: 10px; margin-right: 20px;' width='100'>
                    <div style='flex: 1;'>
                        <h3 class='card-title'>$title</h3>
                        <p class='price'>₱$price</p>
                    </div>
                    <div style='display: flex; align-items: center; gap: 10px;'>
                        <form method='POST' action='user_cart.php' style='display:inline;'>
                            <input type='hidden' name='product_ID' value='$product_ID'>
                            <input type='hidden' name='action' value='decrease'>
                            <button class='btn-custom' ". ($qty <= 1 ? "disabled" : "") .">-</button>
                        </form>
                        <span class='qty'>$qty</span>
                        <form method='POST' action='user_cart.php' style='display:inline;'>
                            <input type='hidden' name='product_ID' value='$product_ID'>
                            <input type='hidden' name='action' value='increase'>
                            <button class='btn-custom' ". ($qty >= $product_quantity ? "disabled" : "") .">+</button>
                        </form>
                    </div>
                    <form method='POST' action='user_update_cart.php' style='display:inline; margin-left: 20px;'>
                        <input type='hidden' name='product_ID' value='$product_ID'>
                        <input type='hidden' name='action' value='remove'>
                        <button class='custom-btn'>Remove</button>
                    </form>
                </div>";
            }
              echo "<div style='text-align: right; margin-top: 30px;'>
                    <h3>Total: ₱$total</h3>
                    <button class='btn-custom' data-bs-toggle='modal' data-bs-target='#checkout-modal' id='checkout-button'>Proceed to Checkout</button>
                    </div>";
          } else {
              echo "<p>Your cart is empty.</p>";
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


      <div class="modal fade" id="checkout-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title" id="staticBackdropLabel">Checkout Confirmation</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <!-- Order Summary Section -->
            <div class="summary-container">
              <h2 style="text-align: center; margin-bottom: 20px; font-weight: bold;">Order Summary</h2>
              
              <!-- Dynamic Cart Items will be listed here -->
              <!-- <div id="order-items"></div> -->

              <!-- Total Price -->
              <!-- <div id="order-total" style="font-weight: bold; text-align: right; margin-top: 20px;"></div> -->
            </div>

            <!-- Payment Method Section -->
            <div class="formcontainer">
              <form id="delivery-form">
                <h5>Select Payment Method:</h5>
                <select name="payment_method" required>
                  <option value="" disabled selected>Select an option</option>
                  <option value="online">Online Payment</option>
                  <option value="cash">Cash on meet-up</option>
                </select>
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn confirm-btn" data-bs-dismiss="modal" onclick="confirmCheckout()">Confirm</button>
          </div>
        </div>
      </div>
    </div>

  <!-- FOOTER -->
  <div class="footer">
    <p>&copy; 2025 Purrfectly Stitched. All rights reserved.</p>
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
