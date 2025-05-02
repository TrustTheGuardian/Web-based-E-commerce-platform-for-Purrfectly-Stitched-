<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db_connection.php';
?>
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
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" 
                                max="<?= $product['product_quantity'] ?>" data-quantity="<?= $product['product_quantity'] ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-secondary" onclick="addToCart(<?= $product_id ?>)">Add to Cart</button>
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
                                <!-- Example review -->
                                <div class="mb-3 border-bottom pb-2">
                                    <strong>Jane Doe</strong>
                                    <div class="star text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star"></i>
                                    </div>
                                    <p>Love the texture and quality!</p>
                                </div>
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
                                <button type="submit" class="btn btn-outline-secondary buttoncolor">Submit Review</button>
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
    const maxQuantity = parseInt(qtyInput.getAttribute('data-quantity'));
    let current = parseInt(qtyInput.value);

    if (!isNaN(current)) {
        let newQuantity = current + delta;
        if (newQuantity >= 1 && newQuantity <= maxQuantity) {
            qtyInput.value = newQuantity;
        }
    }
}

function addToCart(product_ID, stock) {
    const qty = 1; // Or use the quantity from an input field if needed
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_ID', product_ID);
    formData.append('quantity', qty);

    fetch('user_add_to_cart.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
    .then(response => {
        if (response === 'out_of_stock') {
            alert("Sorry, this product is out of stock.");
        } else {
            console.log("Cart count:", response);
            updateCartIcon(response);
            alert("Product added to cart!");
        }
    });
}

    function updateCartIcon(count) {
        const iconContainer = document.querySelector('.bi-cart').parentElement;
        let badge = iconContainer.querySelector('.badge');

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
            iconContainer.appendChild(badge);
        }

        badge.textContent = count;
    }
</script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>