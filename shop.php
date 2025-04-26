<?php
// shop.php
include 'db_connection.php'; // must define $con

// 1) Grab search & category filters from URL
$search      = isset($_GET['search'])     ? trim($_GET['search'])             : '';
$selectedCat = isset($_GET['categoryId']) ? intval($_GET['categoryId'])        : null;

// 2) Fetch all categories
$catsRes = mysqli_query($con, "
    SELECT product_category_ID, category_name
    FROM product_category
    ORDER BY category_name
") or die(mysqli_error($con));

// 3) Determine heading
if ($search !== '') {
    // Searching always overrides the "All"/category heading
    $heading = "Search results for \"" . htmlspecialchars($search) . "\"";
} elseif ($selectedCat) {
    // Category selected, no search
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

// 4) Build WHERE clauses
$where = [];
if ($selectedCat) {
    $where[] = "p.product_category_ID = $selectedCat";
}
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($con, $search);
    $where[]    = "p.product_title LIKE '%{$safeSearch}%'";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";


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
      <form class="search-bar-container mb-4" method="GET" action="">
        <input 
          type="text" 
          name="search" 
          class="search-input" 
          placeholder="Search products…" 
          value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="btn btn-outline-secondary">
          <i class="bi bi-search"></i>
        </button>
      </form>
          
    
    <div class="menu">
        <a href="shop.php" class="d-none d-sm-block">Shop</a>
        <div class="btn-group custom-dropdown">
          <button type="button" class="dropdown-toggle d-sm-block d-none" data-bs-toggle="dropdown">
              Profile
          </button>
          <!-- Dropdown Menu -->
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Log in</a>
              </li>
              <li>
                <a class="dropdown-item" href="registration.php">Create account</a>
              </li>
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


  <div class="shopcategorybar mb-4">
    <nav class="category-bar">
      <a
        href="shop.php"
        class="btn <?= $selectedCat === null ? 'btn-custom' : '' ?>"
      >All</a>
      <?php while ($cat = mysqli_fetch_assoc($catsRes)): ?>
        <a
          href="shop.php?categoryId=<?= $cat['product_category_ID'] ?>"
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
               onclick="window.location.href='aboutproduct.php?productId=<?= $id ?>'">
            <div class="card product-card">
              <img src="<?= htmlspecialchars($img) ?>"
                   class="card-img-top img-thumbnail"
                   alt="<?= $title ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?= $title ?></h5>
                <p class="card-text">₱<?= $price ?></p>
                <a href="#" class="btn btn-custom">Add to cart</a>
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
</body>
</html>