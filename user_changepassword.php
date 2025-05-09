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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css_files/userhome_styles.css">
    <link rel="stylesheet" href="css_files/editprofile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Edit Password</title>
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
        <div class="row containereditprofile">
            <div class="space col-1 d-none d-sm-block d-md-block"></div>
            <div class="editprofilecontainer col-12 col-sm-10">
                <div class="editprofile">
                    <h3>Change Password</h3>

                    <form id="passwordForm" method="POST" action="update_password.php">
                        <div class="formstyle position-relative">
                            <label for="password" class="form-label">Original Password</label>
                            <input type="password" class="form-control" name="original_password" id="password" required>
                            <i class="bi bi-eye-slash toggle-password" data-target="password" 
                                    style="position: absolute; top: 50%; transform: translateY(20%); right: 15px; cursor: pointer;"></i>
                        </div>

                        <div class="formstyle position-relative">
                            <label for="repassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" id="repassword" required>
                            <i class="bi bi-eye-slash toggle-password" data-target="repassword" 
                                    style="position: absolute; top: 50%; transform: translateY(20%); right: 15px; cursor: pointer;"></i>
                        </div>

                        <div class="text-center d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">Save Changes</button>
                        </div>
                    </form>

                    <!-- Save Modal -->
                    <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="saveModalLabel">Confirm Save</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to save the changes?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="confirmSave">Yes, Save</button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Cancel Modal -->
                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="cancelModalLabel">Confirm Cancel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to discard your changes?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <a href="user_profile.php" class="btn btn-danger">Yes, Cancel</a>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src=".vscode/jsforhome.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Password toggle
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            }
        });
    });

    // Confirm Save: submit the form when "Yes, Save" is clicked
    const confirmSave = document.getElementById('confirmSave');
    if (confirmSave) {
        confirmSave.addEventListener('click', function () {
            const originalPassword = document.getElementById('password').value;
            const newPassword = document.getElementById('repassword').value;
            
            if (validatePassword(newPassword)) {
                document.getElementById('passwordForm').submit();
            } else {
                alert("Password does not meet the required criteria.");
            }
        });
    }

    // Password validation function
    function validatePassword(password) {
        const lowerCase = /[a-z]/;
        const upperCase = /[A-Z]/;
        const number = /[0-9]/;
        const specialChar = /[!@#$%^&*(),.?":{}|<>]/;

        if (password.length < 8) {
            alert("Password must be at least 8 characters long.");
            return false;
        }
        if (!lowerCase.test(password)) {
            alert("Password must contain at least one lowercase letter.");
            return false;
        }
        if (!upperCase.test(password)) {
            alert("Password must contain at least one uppercase letter.");
            return false;
        }
        if (!number.test(password)) {
            alert("Password must contain at least one number.");
            return false;
        }
        if (!specialChar.test(password)) {
            alert("Password must contain at least one special character.");
            return false;
        }
        return true;
    }
});
</script>

</body>
</html>
