<?php
include 'db_connection.php'; 

if (!isset($_GET['productId'])) {
    exit("No product specified.");
}

$product_id = intval($_GET['productId']);

$query = "
  SELECT p.*, pi.image_path
  FROM products p
  LEFT JOIN product_images pi 
    ON p.product_ID = pi.product_ID
  WHERE p.product_ID = $product_id
  LIMIT 1
";
$result = mysqli_query($con, $query) 
    or exit("Database error: " . mysqli_error($con));

if (mysqli_num_rows($result) === 0) {
    exit("Product not found.");
}

$product = mysqli_fetch_assoc($result);
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

// Load all reviews for the product
$review_query = $con->prepare("SELECT r.rating, r.review_text, r.created_at, u.FirstName, u.LastName, r.admin_reply 
                               FROM reviews r 
                               JOIN users u ON r.user_ID = u.user_ID 
                               WHERE r.product_ID = ? 
                               ORDER BY r.created_at DESC");
$review_query->bind_param("i", $product_id);
$review_query->execute();
$review_result = $review_query->get_result();

// Get average rating and total reviews
$avg_rating = 0;
$total_reviews = 0;

$rating_query = $con->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_ID = ?");
$rating_query->bind_param("i", $product_id);
$rating_query->execute();
$rating_result = $rating_query->get_result();

if ($rating_result && $rating_result->num_rows > 0) {
    $rating_data = $rating_result->fetch_assoc();
    $avg_rating = round($rating_data['avg_rating'], 1);
    $total_reviews = $rating_data['total_reviews'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About product</title>
    <link rel="stylesheet" href="css_files/userhome_styles.css">
    <link rel="stylesheet" href="css_files/aboutproduct.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<body>
    <header class="header col-12">
        <div class="logo">
            <a href="index.php"><img src="pictures/Purrfectly Stitch.png" alt="sample logo" width="60px" height = "50px">
            <h2>Purrfectly Stitched</h2></a>
        </div>
    
        <div class="menu">
            <a href="shop.php" class="d-none d-sm-block">Shop</a>
            <div class="btn-group custom-dropdown">
              <button type="button" class="dropdown-toggle d-sm-block d-none" data-bs-toggle="dropdown">
                  Profile
              </button>
              <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#loginModal" data-bs-toggle="modal">Log in</a></li>
                  <li><a class="dropdown-item" href="registration.php">Create account</a></li>
              </ul>
              
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
          <i class="bi bi-cart"></i>
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
          
        </div>
    </header>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Log in</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Your login form goes here -->
            <form id="loginForm" action="login.php" method="POST">
              <div class="mb-3">
                <label for="loginEmail" class="form-label">Email address</label>
                <input placeholder="Enter your Email" type="email" class="form-control" name="Email" id="loginEmail" required>
              </div>
              <div class="mb-3">
                <label for="visiblePassword" class="form-label">Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="Password" id="visiblePassword" placeholder="Enter your password" minlength="8" required>
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('visiblePassword', this)">
                    <i class="bi bi-eye-slash"></i>
                  </button>
                </div>
              </div>
              <button type="submit" class="custom-btn">Log in</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section>

    <div class="d-flex flex-column flex-md-row">
            <div class="carouselcontainer col-12 col-md-6">
            <?php
                    // 1) Grab all images for this product
                    $imgResult = mysqli_query(
                        $con,
                        "SELECT image_path 
                        FROM product_images 
                        WHERE product_ID = $product_id
                        ORDER BY image_ID ASC"
                    ) or exit("Image query failed: " . mysqli_error($con));

                    $images = [];
                    while ($row = mysqli_fetch_assoc($imgResult)) {
                        $images[] = $row['image_path'];
                    }
                    ?>

                <!-- 2) Dynamic Carousel -->
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($images as $idx => $imgPath): ?>
                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($imgPath) ?>"
                            class="d-block w-100"
                            alt="Product image <?= $idx + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                </div>

                <!-- 3) Dynamic Thumbnail Gallery -->
                <div class="thumbnail">
                <div class="thumbnail-gallery d-flex justify-content-center mt-3 gap-2">
                    <?php foreach ($images as $idx => $imgPath): ?>
                    <img
                        src="<?= htmlspecialchars($imgPath) ?>"
                        class="thumb-img"
                        data-bs-target="#carouselExampleCaptions"
                        data-bs-slide-to="<?= $idx ?>"
                        aria-label="Go to slide <?= $idx + 1 ?>"
                        style="cursor:pointer;"
                        alt="Thumbnail <?= $idx + 1 ?>"
                    >
                    <?php endforeach; ?>
                </div>
                </div>

              </div>
              <div class="col-12 col-md-6">
                <div class="aboutproduct">
                    <!-- Title -->
                    <h1><?= htmlspecialchars($product['product_title']) ?></h1>
                    <p class="text-success mt-2"><?= $product['product_quantity'] > 0 ? 'In stock' : 'Out of stock' ?></p>
                    <p><?= htmlspecialchars($product['product_description']) ?></p>
                    <br>
                    <div class="pricerating">
                    <h3>₱<?= number_format($product['product_price'], 2) ?></h3>
                        <div class="star">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <i class="bi bi-star"></i>
                            <small class="text-muted">(12 reviews)</small>
                        </div>
                    </div>
                    <hr>
                    <div class="cartwithquantity">
                        <div class="mt-3 d-flex align-items-center gap-2">
                            <label for="quantity" class="form-label mb-0">Quantity:</label>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Add to Cart
                            </button>
                        </div>
                    </div>
                    <div class="productinfo">
                        <div class="accordion mt-4" id="productDetails">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Product Details
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <p><?= htmlspecialchars($product['product_description']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reviewsection">
                    <div class="review-list mt-5">
                    <h4>Customer Reviews</h4>
                    <div id="reviewsContainer">
                        <?php if ($review_result->num_rows > 0): ?>
                            <?php while ($row = $review_result->fetch_assoc()): ?>
                                <div class="mb-3 border-bottom pb-2">
                                    <strong><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></strong>
                                    <div class="star text-warning">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $row['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($row['review_text']); ?></p>

                                    <?php if (!empty($row['admin_reply'])): ?>
                                        <div class="admin-reply text-primary">
                                            <strong>Admin Reply:</strong> <?= htmlspecialchars($row['admin_reply']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No reviews yet. Be the first to review!</p>
                        <?php endif; ?>
                    </div>
                </div>
                                        
                        <!-- Review Form -->
                        <div class="mt-4">
                            <h5>Leave a Review</h5>
                            <form id="reviewForm">

                                <div class="mb-2">
                                    <label class="form-label">Your Rating</label>
                                    <div id="starRating" class="text-warning fs-5">
                                        <!-- Stars will be interactive -->
                                        <i class="bi bi-star" data-rating="1"></i>
                                        <i class="bi bi-star" data-rating="2"></i>
                                        <i class="bi bi-star" data-rating="3"></i>
                                        <i class="bi bi-star" data-rating="4"></i>
                                        <i class="bi bi-star" data-rating="5"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="reviewText" class="form-label">Your Review</label>
                                    <textarea class="form-control" id="reviewText" rows="3" required></textarea>
                                </div>
                                <button type="button" class="btn btn-outline-secondary buttoncolor" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    Submit Review
                                    </button>
                            </form>
                        </div>
                    </div>
                                     
                </div>
            </div>
        </div>
    </section>

    <script>
        let selectedRating = 0;
    
        // Handle star rating selection
        const stars = document.querySelectorAll('#starRating i');
        stars.forEach(star => {
            star.addEventListener('click', () => {
                selectedRating = parseInt(star.getAttribute('data-rating'));
                stars.forEach(s => {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                });
                for (let i = 0; i < selectedRating; i++) {
                    stars[i].classList.remove('bi-star');
                    stars[i].classList.add('bi-star-fill');
                }
            });
        });
    
        // Handle review form submission
        const reviewForm = document.getElementById('reviewForm');
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = document.getElementById('reviewText').value;
    
            if (selectedRating === 0) {
                alert("Please select a rating.");
                return;
            }
    
            const container = document.getElementById('reviewsContainer');
            const reviewHTML = `
                <div class="mb-3 border-bottom pb-2">
                    <strong>${name}</strong>
                    <div class="star text-warning">
                        ${'<i class="bi bi-star-fill"></i>'.repeat(selectedRating)}
                        ${'<i class="bi bi-star"></i>'.repeat(5 - selectedRating)}
                    </div>
                    <p>${text}</p>
                </div>
            `;
            container.innerHTML += reviewHTML;
    
            // Clear form
            reviewForm.reset();
            selectedRating = 0;
            stars.forEach(s => {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            });
        });
    </script>


    <script>
        function changeQuantity(delta) {
        const qtyInput = document.getElementById('quantity');
        let current = parseInt(qtyInput.value);
        if (!isNaN(current)) {
            qtyInput.value = Math.max(1, current + delta); // prevent going below 1
        }}
    </script>

    <script>
      function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector("i");

        if (input.type === "password") {
          input.type = "text";
          icon.classList.remove("bi-eye-slash");
          icon.classList.add("bi-eye");
        } else {
          input.type = "password";
          icon.classList.remove("bi-eye");
          icon.classList.add("bi-eye-slash");
        }
      }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>