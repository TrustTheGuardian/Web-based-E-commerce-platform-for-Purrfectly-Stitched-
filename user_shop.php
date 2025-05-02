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

$search      = isset($_GET['search'])     ? trim($_GET['search'])             : '';
$selectedCat = isset($_GET['categoryId']) ? intval($_GET['categoryId'])        : null;

$catsRes = mysqli_query($con, "
    SELECT product_category_ID, category_name
    FROM product_category
    ORDER BY category_name
") or die(mysqli_error($con));

if ($search !== '') {
    $heading = "Search results for \"" . htmlspecialchars($search) . "\"";
} elseif ($selectedCat) {
    $hRes = mysqli_query($con, "
      SELECT category_name 
      FROM product_category 
      WHERE product_category_ID = $selectedCat
    ") or die(mysqli_error($con));
    if ($hRow = mysqli_fetch_assoc($hRes)) {
        $heading = htmlspecialchars($hRow['category_name']);
    } else {
        $heading = "All Products";
    }
} else {
    $heading = "All Products";
}

$where = [];
if ($selectedCat) {
    $where[] = "p.product_category_ID = $selectedCat";
}
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($con, $search);
    $where[]    = "p.product_title LIKE '%{$safeSearch}%'";
}

// 5) Final product query
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "
  SELECT 
    p.product_ID, 
    p.product_title, 
    p.product_price, 
    pi.image_path
  FROM products p
  LEFT JOIN product_images pi 
    ON p.product_ID = pi.product_ID
  $whereSQL
  " . ($whereSQL ? "AND " : "WHERE ") . " p.product_status = 'active' 
  ORDER BY p.product_title
";
$prodRes = mysqli_query($con, $sql) or die(mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Purrfectly Stitched – Shop</title>
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

    <?php
      // Grab the search term, if any
      $search = isset($_GET['search']) 
          ? trim($_GET['search']) 
          : '';
      ?>
      <form class="search-bar-container" method="GET" action="">
        <input 
          type="text" 
          name="search" 
          class="search-input" 
          placeholder="Search products…" 
          value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="btn">
          <i class="bi bi-search"></i>
        </button>
      </form>
          
    
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
        </div>
    </header>



  <div class="shopcategorybar mb-4">
    <nav class="category-bar">
      <a
        href="user_shop.php"
        class="btn <?= $selectedCat === null ? 'btn-custom' : '' ?>"
      >All</a>
      <?php while ($cat = mysqli_fetch_assoc($catsRes)): ?>
        <a
          href="user_shop.php?categoryId=<?= $cat['product_category_ID'] ?>"
          class="btn <?= $selectedCat === (int)$cat['product_category_ID'] ? 'btn-custom' : '' ?>"
        >
          <?= htmlspecialchars($cat['category_name']) ?>
        </a>
      <?php endwhile; ?>
    </nav>
  </div>

  <!-- Heading -->
  <div class="categoryname mb-3">
    <h2><?= htmlspecialchars($heading) ?></h2>
  </div>

  <!-- Products Grid -->
  <section id="products">
    <div class="row mt-2">
      <?php if (mysqli_num_rows($prodRes) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($prodRes)): 
          $id    = $row['product_ID'];
          $title = htmlspecialchars($row['product_title']);
          $price = number_format($row['product_price'], 2);
          // fallback if no image
          $img   = $row['image_path'] ?: 'fallback.jpg';
        ?>
          <div class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-4"
               onclick="window.location.href='user_aboutproduct.php?productId=<?= $id ?>'">
            <div class="card product-card">
            <img src="<?= htmlspecialchars($img) ?>"
                   class="card-img-top img-thumbnail "
                   alt="<?= $title ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?= $title ?></h5>
                <p class="card-text">₱<?= $price ?></p>
                <a href="#" class="btn btn-custom" onclick="event.stopPropagation(); addToCart(<?= $id ?>)">Add to cart</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p>No products found in this category.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
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

</body>
</html>