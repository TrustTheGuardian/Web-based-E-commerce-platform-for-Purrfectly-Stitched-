<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: index.php');
    exit;
}
include 'db_connection.php';

$userID = $_SESSION['user_id'];
$query = "SELECT ProfileImage FROM users WHERE user_ID = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $profileImage = !empty($user['ProfileImage']) ? $user['ProfileImage'] : 'pictures/default-profile.png';
} else {
    $profileImage = 'pictures/default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Purrfectly Stitched – Welcome Back</title>
  <link rel="stylesheet" href="css_files/userhome_styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
    
    <section id="slider" class="d-flex flex-column flex-md-row align-items-center justify-content-center mt-5">
    <div class="welcometextdesign col-12 col-md-6 mb-4">
        <h1><br>Your Crochet Products</h1>
        <p>Shop now and customize your own Crochet Products</p>
        <a href="user_shop.php" class="btn btn-custom">Shop Now &#10140;</a>
    </div>
    <div class="carouselcontainer col-12 col-md-6">
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                include 'db_connection.php';
                // Fetch all banners from the database
                $result = mysqli_query($con, "SELECT * FROM banners ORDER BY banner_id DESC");
                $first = true; // Flag to mark the first item as active
                while ($row = mysqli_fetch_assoc($result)) {
                    $activeClass = $first ? 'active' : ''; // Add "active" class to the first item
                    echo '<div class="carousel-item ' . $activeClass . '">';
                    echo '<img src="' . $row['image_path'] . '" class="d-block w-100" alt="...">';
                    echo '<div class="carousel-caption">';
                    echo '<h5>' . htmlspecialchars($row['image_title']) . '</h5>';
                    echo '<p>' . htmlspecialchars($row['image_description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    $first = false; // Set flag to false after the first item
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<section id="announcement-section">
    <h2>Announcements!</h2>
    <div class="announcement-scroll-container">
        <?php
        // Fetch all announcements from the database
        $res = mysqli_query($con, "SELECT * FROM announcements ORDER BY announcement_date DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<div class='announcement-card'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p class='announcement-date'>" . date("F j, Y", strtotime($row['announcement_date'])) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
            echo "</div><hr>";
        }
        ?>
    </div>
</section>
    
          <section id="products">   
        <div class="row mt-5">         
          <?php
          include 'db_connection.php'; 

          $result = mysqli_query($con, "
            SELECT p.product_ID, p.product_title, p.product_price, pi.image_path
            FROM products p
            LEFT JOIN product_images pi ON p.product_ID = pi.product_ID
            WHERE p.product_status = 'active'
            GROUP BY p.product_ID
          ") or die(mysqli_error($con));

          if ($result && mysqli_num_rows($result) > 0):
          ?>
            <div class="row mt-5">
              <?php while ($row = mysqli_fetch_assoc($result)): 
                $productID = $row['product_ID'];
                $title     = htmlspecialchars($row['product_title']);
                $price     = number_format($row['product_price'], 2);
                $image     = $row['image_path'];
              ?>
                <div 
                  class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-2"
                  onclick="goToProductPage(event, <?= $productID ?>)"
                >
                  <div class="card product-card">
                    <img src="<?= $image ?>" 
                        class="card-img-top img-thumbnail" 
                        alt="<?= $title ?>">
                    <div class="card-body">
                      <h5 class="card-title"><?= $title ?></h5>
                      <p class="card-text">₱<?= $price ?></p>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No products available.</p>
          <?php endif; ?>
        </div>
      </section>


    <div class="footer">
      &copy; 2025 Your Website | All Rights Reserved
    </div>


    <script>
      function addToCart(product_ID) {
        const qty = document.getElementById('quantity').value;
        const formData = new FormData();
        formData.append('product_ID', product_ID);
        formData.append('quantity', qty);

        fetch('user_add_to_cart.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
        .then(count => {
            console.log("Cart count:", count); // <- this helps
            updateCartIcon(count);
            alert("Product added to cart!");
        });
    }
    </script>

    <script>
    function goToProductPage(event, productId) {
      if (event.target.closest('.add-to-cart')) return;
      window.location.href = "user_aboutproduct.php?productId=" + productId;
    }
    </script>
    
    
    <script src=".vscode/jsforhome.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>