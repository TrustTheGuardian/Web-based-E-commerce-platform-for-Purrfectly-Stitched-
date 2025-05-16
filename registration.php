<?php 
include("db_connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css_files/index.css">
</head>
<body class="grad">
  <div class="container">
    <div class="row justify-content-center">

<?php 
$show_success_modal = false;

if(isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $house_street = $_POST['house_street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postal_code = $_POST['postal_code'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $full_address = "$house_street, $barangay, $city, $province, $postal_code";

    $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

    if(mysqli_num_rows($verify_query) != 0) {
        echo "<div class='message'>
        <p style='color: red;'>This email is already taken.</p>
            </div><br>";
      echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
    } else {
        if($password !== $confirm_password) {
            echo "<div class='message'>
                    <p>Passwords do not match.</p>
                  </div><br>";
            echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($con, "INSERT INTO users (FirstName, LastName, Gender, Email, Mobile, Address, Password) 
            VALUES ('$first_name', '$last_name', '$gender', '$email', '$mobile', '$full_address', '$hashed_password')") 
            or die("Error Occurred");

            $show_success_modal = true;
        }
    }
}

if(!isset($_POST['submit']) || !$show_success_modal) {
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-sm">
        <div class="card-header text-center text-white">
          <h4>Sign Up</h4>
        </div>
        <div class="card-body">
          <form id="registrationForm" action="" method="post">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="FirstName" class="form-label">First Name</label>
                <input type="text" class="form-control" name="first_name" id="Fname" placeholder="Enter your First Name" required>
              </div>
              <div class="col-md-6">
                <label for="LastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="last_name" id="Lname" placeholder="Enter your Last Name" required>
              </div>
              <div class="col-md-4">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" name="gender" id="gender" required>
                  <option value="" disabled selected>Select your gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                  <option value="Prefer not to say">Prefer not to say</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required onkeyup="checkEmail()">
                <div id="emailFeedback" class="form-text text-danger"></div>
              </div>
              <div class="col-md-4">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Enter your mobile number" 
                  pattern="0[0-9]{10}" required maxlength="11" oninput="validateMobile()" >
                <small class="form-text text-muted">Please enter an 11-digit mobile number starting with 0</small>
                <div id="mobileFeedback" class="form-text text-danger"></div>
              </div>
              <div class="col-md-4">
                <label for="houseStreet" class="form-label">House No./Blk or Lot No., Street</label>
                <input type="text" class="form-control" name="house_street" id="houseStreet" placeholder="e.g., Blk 5 Lot 3, Sampaguita St." required>
              </div>
              <div class="col-md-4">
                <label for="barangay" class="form-label">Barangay</label>
                <input type="text" class="form-control" name="barangay" id="barangay" placeholder="e.g., Barangay San Isidro" required>
              </div>
              <div class="col-md-4">
                <label for="city" class="form-label">City/Municipality</label>
                <input type="text" class="form-control" name="city" id="city" placeholder="e.g., Quezon City" required>
              </div>
              <div class="col-md-6">
                <label for="province" class="form-label">Province</label>
                <input type="text" class="form-control" name="province" id="province" placeholder="e.g., Metro Manila" required>
              </div>
              <div class="col-md-6">
                <label for="postalCode" class="form-label">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" id="postalCode" placeholder="e.g., 1100" required>
              </div>
              <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" minlength="8" required oninput="validatePassword()">
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye-slash"></i>
                  </button>
                </div>
                <div id="passwordFeedback" class="form-text text-danger"></div>
              </div>
              <div class="col-md-6">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="Re-enter your password" minlength="8" required>
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', this)">
                    <i class="bi bi-eye-slash"></i>
                  </button>
                </div>
              </div>
              <div class="col-12">
                <div id="passwordError" class="text-danger text-center" style="display: none;">
                  Passwords do not match.
                </div>
              </div>
              <div class="col-12 d-grid mt-3">
                <button type="submit" class="custom-btn btn btn-primary" name="submit">Register</button>
              </div>
            </div>
          </form>
        </div>
        <div class="card-footer text-center">
          Already have an account? <a href="index.php" class="text-primary">Sign In</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php } ?>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content registration-success-modal shadow-sm">
      <div class="modal-header border-0">
        <h3 class="modal-title w-100 text-center">🎉 Registration Successful!</h3>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        <p class="fs-5 mt-3">Your account has been successfully registered.</p>
        <a href="index.php" class="btn confirm-btn mt-3 px-4 py-2">Login Now</a>
      </div>
    </div>
  </div>
</div>

<?php if ($show_success_modal): ?>
<script>
  window.onload = function() {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
  };
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const noNumbers = (event) => {
      const input = event.target;
      input.value = input.value.replace(/[0-9]/g, ''); // Remove any digits
    };

    document.getElementById('Fname').addEventListener('input', noNumbers);
    document.getElementById('Lname').addEventListener('input', noNumbers);
  });
function validatePassword() {
  const password = document.getElementById("password").value;
  const feedback = document.getElementById("passwordFeedback");

  const lowercase = /[a-z]/;
  const uppercase = /[A-Z]/;
  const number = /[0-9]/;
  const specialChar = /[!@#$%^&*(),.?":{}|<>]/;

  let errors = [];

  if (password.length < 8) errors.push("at least 8 characters");
  if (!lowercase.test(password)) errors.push("a lowercase letter");
  if (!uppercase.test(password)) errors.push("an uppercase letter");
  if (!number.test(password)) errors.push("a number");
  if (!specialChar.test(password)) errors.push("a special character");

  if (errors.length > 0) {
    feedback.innerHTML = "Password must contain: " + errors.join(", ");
    document.getElementById("password").setCustomValidity("Invalid password");
  } else {
    feedback.innerHTML = "";
    document.getElementById("password").setCustomValidity("");
  }
}

function togglePassword(inputId, button) {
  var input = document.getElementById(inputId);
  var icon = button.querySelector("i");

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

function checkEmail() {
  const email = document.getElementById("email").value;
  const feedback = document.getElementById("emailFeedback");

  const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

  if (!emailPattern.test(email)) {
    feedback.innerHTML = "Please enter a valid email address.";
  } else {
    feedback.innerHTML = "";
  }
}

function checkMobile() {
  const mobile = document.getElementById("mobile").value;
  const feedback = document.getElementById("mobileFeedback");

  if (!/^0[0-9]{10}$/.test(mobile)) {
    feedback.innerHTML = "Mobile number must be 11 digits and start with 0.";
  } else {
    feedback.innerHTML = "";
  }
}


function formatMobile() {
    const mobileInput = document.getElementById("mobile");
    let mobile = mobileInput.value;

    if (mobile.length > 11) {
      mobile = mobile.slice(0, 11);
      mobileInput.value = mobile;
    }


  }

  function checkMobile() {

  }

  function validateMobile() {
    const mobileInput = document.getElementById("mobile");
    const feedback = document.getElementById("mobileFeedback");
    const mobile = mobileInput.value;

    // Reset feedback
    feedback.textContent = "";

    // Enforce max length 11
    if (mobile.length > 11) {
      mobileInput.value = mobile.slice(0, 11);
      return;
    }

    // Check if first character is 0
    if (mobile.length > 0 && mobile.charAt(0) !== '0') {
      feedback.textContent = "Mobile number must start with 0.";
      mobileInput.setCustomValidity("Mobile number must start with 0.");
    } 
    else if (mobile.length > 0 && !/^0\d*$/.test(mobile)) {
      // Optional: reject non-digits after first char (if needed)
      feedback.textContent = "Mobile number can only contain digits.";
      mobileInput.setCustomValidity("Mobile number can only contain digits.");
    }
    else if (mobile.length !== 11) {
      // If you want to enforce length during typing (optional)
      feedback.textContent = "Mobile number must be exactly 11 digits.";
      mobileInput.setCustomValidity("Mobile number must be exactly 11 digits.");
    }
    else {
      // Valid input
      feedback.textContent = "";
      mobileInput.setCustomValidity("");
    }
  }
</script>
</body>
</html>
