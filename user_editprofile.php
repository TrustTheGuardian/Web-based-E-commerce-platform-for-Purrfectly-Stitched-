<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: index.php');
    exit;
}
$userID = $_SESSION['user_id']; // ✅ Add this line
include 'db_connection.php';

// Fetch user data
$query = "SELECT * FROM users WHERE user_ID = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css_files/userhome_styles.css">
    <link rel="stylesheet" href="css_files/editprofile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Edit Profile</title>
</head>
<body>

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


    <section class="mt-3 mb-5">
        <div class="container-fluid">
        <div class="editprofilecontainer col-12 col-sm-10">
            <div class="editprofile">
                <h3>Edit profile</h3>
                <img src="<?= $user['ProfileImage'] ?? 'pictures/man-user-circle-icon.png' ?>" alt="Profile Picture" class="rounded-circle" width="120" height="120">

                <form id="editProfileForm" action="save_profile.php" method="POST" enctype="multipart/form-data" class="text-start">
                    <input type="file" name="profileImage" accept="image/*" class="form-control mt-3" style="max-width: 250px;">

                    <div class="formstyle">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullName" value="<?= $user['FirstName'] . ' ' . $user['LastName'] ?>">
                    </div>

                    <div class="formstyle">
                      <label for="gender" class="form-label">Gender</label>
                      <select name="gender" class="form-control">
                          <option value="" disabled <?= empty($user['Gender']) ? 'selected' : '' ?>>Select your gender</option>
                          <option value="Male" <?= $user['Gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= $user['Gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                          <option value="Other" <?= $user['Gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                          <option value="Prefer not to say" <?= $user['Gender'] === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                      </select>
                  </div>

                    <div class="formstyle">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Enter your mobile number" 
                            pattern="0[0-9]{10}" value="<?= $user['Mobile'] ?>" required oninput="formatMobile()">
                        <small class="form-text text-muted">Please enter an 11-digit mobile number starting with 0</small>
                    </div>

                    <div class="formstyle">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" value="<?= $user['Address'] ?>">
                    </div>

                    <div class="text-center d-flex justify-content-center gap-3">
                  <!-- Cancel Button -->
                  <a href="#" class="btn btn-secondary" style="font-size: 18px;" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel</a>

                  <!-- Save Changes Button -->
                  <button type="button" class="btn btn-primary" style="font-size: 18px;" data-bs-toggle="modal" data-bs-target="#saveModal">Save Changes</button>
              </div>
                              </form>
            </div>
        </div>
                <div class="space col-1 d-none d-sm-block d-md-block "></div>
            </div>
        </div>
    </section>

    <!-- Cancel Modal -->
      <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="cancelModalLabel">Cancel Editing</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      Are you sure you want to exit without saving your changes?
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Stay</button>
                      <a href="user_profile.php" class="btn btn-danger1">Yes, Exit</a>
                  </div>
              </div>
          </div>
      </div>

      <!-- Save Modal -->
      <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="saveModalLabel">Save Changes</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      Are you sure you want to save these changes?
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary1" form="editProfileForm">Yes, Save</button>
                  </div>
              </div>
          </div>
      </div>

      <script>
    function formatMobile() {
        const mobileInput = document.getElementById("mobile");
        let mobileValue = mobileInput.value;

        // Remove all non-numeric characters
        mobileValue = mobileValue.replace(/\D/g, '');

        // Ensure the number starts with 0
        if (mobileValue.charAt(0) !== '0') {
            mobileValue = '0' + mobileValue;
        }

        // Limit to 11 digits
        if (mobileValue.length > 11) {
            mobileValue = mobileValue.slice(0, 11);
        }

        // Set the cleaned value back
        mobileInput.value = mobileValue;
    }
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