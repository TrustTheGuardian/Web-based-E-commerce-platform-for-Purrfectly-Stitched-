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

    <section class="mt-3 mb-5">
    <div class="container-fluid">
        <div class="row containereditprofile">
            <div class="editprofilecontainer"></div>
            <div class="space col-1 d-none d-sm-block d-md-block"></div>
            <div class="editprofilecontainer col-12 col-sm-10">

                <div class="editprofile">
                    <h3>Forgot Password</h3>

                    <form action="send_reset_link.php" method="POST">
                        <label>Email address</label>
                        <input type="email" name="email" required>
                        <button type="submit">Send Reset Link</button>
                    </form>

                </div>
            </div>
            <div class="space col-1 d-none d-sm-block d-md-block"></div>
        </div>
    </div>
</section>

    <!-- Cancel Modal -->
    <!-- <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
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
              <a href="profile.html" class="btn btn-danger1">Yes, Exit</a>
            </div>
          </div>
        </div>
    </div> -->
    
    <!-- Save Modal -->
    <!-- <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
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
    </div> -->

    <!-- Scripts -->
    <script src=".vscode/jsforhome.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = document.getElementById(this.getAttribute('data-target'));
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });
    </script>

</body>
</html>
