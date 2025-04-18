<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purrfectly Stitched - Home</title>
    <link rel="stylesheet" href="css_files/userhome_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
      <?php
      session_start();
      include 'db_connection.php'; // Make sure this connects to your database

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $email = $_POST["Email"];
          $password = $_POST["Password"];

          // Securely check the email in your database
          $email = mysqli_real_escape_string($conn, $email);
          $sql = "SELECT * FROM users WHERE Email = '$email'";
          $result = mysqli_query($conn, $sql);

          if ($result && mysqli_num_rows($result) == 1) {
              $user = mysqli_fetch_assoc($result);

              // Verify hashed password
              if (password_verify($password, $user['Password'])) {
                  $_SESSION['user_id'] = $user['user_ID'];
                  $_SESSION['user_email'] = $user['Email'];

                  // Redirect to homepage or user dashboard
                  header("Location: admin_dashboard.php");
                  exit();
              } else {
                  echo "<script>alert('Incorrect password.'); window.history.back();</script>";
              }
          } else {
              echo "<script>alert('Email not found.'); window.history.back();</script>";
          }
      }
      ?>

  <header class="header col-12">
    <div class="logo">
        <a href="index.php"><img src="pictures/Purrfectly Stitch.png" alt="sample logo" width="60px" height = "50px">
        <h2>Purrfectly Stitched</h2></a>
    </div>

    <div class="menu">
        <a href="shop.html" class="d-none d-sm-block">Shop</a>
        <div class="btn-group custom-dropdown">
          <button type="button" class="dropdown-toggle d-sm-block d-none" data-bs-toggle="dropdown">
              Profile
          </button>
          <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#loginModal" data-bs-toggle="modal">Log in</a></li>
              <li><a class="dropdown-item" href="registration.php">Create account</a></li>
          </ul>
          
        </div>
        <a href="cart.html"><i class="bi bi-cart"></i></a>
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
                <input placeholder="Enter your Email" type="email" class="form-control" name="email" id="loginEmail" required>
              </div>
              <div class="mb-3">
                <label for="visiblePassword" class="form-label">Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="password" id="visiblePassword" placeholder="Enter your password" minlength="8" required>
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
    

    <section id="slider" class="d-flex flex-column flex-md-row align-items-center justify-content-center mt-5">
        <div class="welcometextdesign col-12 col-md-6 mb-4">
            <h1><br>Your Crochet Products</h1>
            <p>Shop now and customize your own Crochet Products</p>
            <a href="shop.html" class="btn btn-custom">Shop Now &#10140;</a>
        </div>
        <div class="carouselcontainer col-12 col-md-6">
          <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="https://img.freepik.com/free-photo/wool-elements-space-right_23-2147691749.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                  <h5>First slide label</h5>
                  <p>Some representative placeholder content for the first slide.</p>
                </div>
              </div>
              <div class="carousel-item">
                <img src="https://static.vecteezy.com/system/resources/thumbnails/023/801/960/small/crochet-and-knitting-hobby-colorful-balls-of-yarn-knitting-needles-on-table-with-copy-space-flat-lay-and-wood-background-photo.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                  <h5>Second slide label</h5>
                  <p>Some representative placeholder content for the second slide.</p>
                </div>
              </div>
              <div class="carousel-item">
                <img src="https://img.freepik.com/premium-photo/crochet-landscape-art-showcasing-vibrant-trees-flowers-serene-scene_1033130-32880.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                  <h5>Third slide label</h5>
                  <p>Some representative placeholder content for the third slide.</p>
                </div>
              </div>
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
        <div class="announcement-card">
            <h3>Fresh Start!</h3>
            <p class="announcement-date">August 22, 2024</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur, dolore.</p>
        </div>

        <div class="announcement-card">
            <h3>Ber-Months Sale</h3>
            <p class="announcement-date">September 1, 2024</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id, sit.</p>
        </div>

        <div class="announcement-card">
            <h3>New Arrivals</h3>
            <p class="announcement-date">December 1, 2024</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates, dicta.</p>
        </div>

        <div class="announcement-card">
            <h3>Special Weekend Discounts</h3>
            <p class="announcement-date">December 5, 2024</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut, quisquam!</p>
        </div>
      </div>
    </section>
    
    <section id="products">   
      <div class="row mt-5">
        <div class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-2" onclick="goToProductPage(event)">
          <div class="card product-card">
            <img src="https://thewoobles.com/cdn/shop/files/88BKY-1b_600x600@2x.jpg?v=1689725518" class="card-img-top img-thumbnail" alt="...">
            <div class="card-body">
              <h5 class="card-title">Crochet Products</h5>
              <p class="card-text">₱159.00</p>
            </div>
          </div>
        </div>          
        <div class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-2 " onclick="goToProductPage(event)">
          <div class="card product-card">
            <img src="https://thewoobles.com/cdn/shop/files/88BKY-1b_600x600@2x.jpg?v=1689725518" class="card-img-top img-thumbnail" alt="...">
            <div class="card-body">
              <h5 class="card-title">Crochet Products</h5>
              <p class="card-text">₱159.00</p>
            </div>
          </div>
        </div>
        <div class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-2 " onclick="goToProductPage(event)">
          <div class="card product-card">
            <img src="https://thewoobles.com/cdn/shop/files/88BKY-1b_600x600@2x.jpg?v=1689725518" class="card-img-top img-thumbnail" alt="...">
            <div class="card-body">
              <h5 class="card-title">Crochet Products</h5>
              <p class="card-text">₱159.00</p>
            </div>
          </div>
        </div>
        <div class="productspacing col-12 col-md-6 col-xl-3 d-flex justify-content-center mb-2 " onclick="goToProductPage(event)">
          <div class="card product-card">
            <img src="https://thewoobles.com/cdn/shop/files/88BKY-1b_600x600@2x.jpg?v=1689725518" class="card-img-top img-thumbnail" alt="...">
            <div class="card-body">
              <h5 class="card-title">Crochet Products</h5>
              <p class="card-text">₱159.00</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="footer">
      &copy; 2025 Your Website | All Rights Reserved
    </div>


    
    <!-- <script>
      document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault(); // Stop form from submitting normally
    
        const email = document.getElementById("loginEmail").value;
        const password = document.getElementById("visiblePassword").value; // FIXED ID
    
        // Check if guest entered the specific credentials
        if (email === "Yoonieshin21@gmail.com" && password === "ilovemeshin2121") {
          window.location.href = "admin_dashboard.html"; // Redirect to admin dashboard
        } else {
          alert("Invalid email or password.");
        }
      });
    </script> -->

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

    <script>
    function goToProductPage(event) {
    // Prevent navigation if "Add to cart" was clicked
    if (event.target.closest('.add-to-cart')) return;

    // Replace with your actual product page URL
    window.location.href = "aboutproduct.html";}
    </script>
    
    
    <script src=".vscode/jsforhome.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>