<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS Style -->
    <link rel="stylesheet" href="css_files/index.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="grad">
    <div class="container">
        <div class="row justify-content-center">
    <?php 
            include("db_connection.php");
            if(isset($_POST['submit'])){

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

                // Combine address parts into a single string
                $full_address = "$house_street, $barangay, $city, $province, $postal_code";

                // Verifying if the email is unique
                $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

                if(mysqli_num_rows($verify_query) != 0) {
                    echo "<div class='message'>
                            <p>This email is already in use. Try another one.</p>
                        </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                } else {
                    // Check if password and confirm password match
                    if($password !== $confirm_password) {
                        echo "<div class='message'>
                                <p>Passwords do not match.</p>
                            </div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                    } else {
                        // Hash the password before storing
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        mysqli_query($con, "INSERT INTO users (FirstName, LastName, Gender, Email, Mobile, Address, Password) 
                        VALUES ('$first_name', '$last_name', '$gender', '$email', $mobile, '$full_address', '$hashed_password')") 
                        or die("Error Occurred");

                        echo "<div class='message'>
                                <p>Registration successful!</p>
                            </div> <br>";
                        echo "<a href='home.html'><button class='btn'>Login Now</button></a>";
                    }
                }
            } else {
        ?>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-sm">
                            <!-- Card Header -->
                            <div class="card-header text-center text-white">
                                <h4>Sign Up</h4>
                            </div>
            
                            <div class="card-body">
                                <form id="registrationForm" action="" method="post">
                                    <div class="row g-3">
                                        <!-- First Name -->
                                        <div class="col-md-6">
                                            <label for="FirstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" id="Fname" placeholder="Enter your First Name" required>
                                        </div>
            
                                        <!-- Last Name -->
                                        <div class="col-md-6">
                                            <label for="LastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" id="Lname" placeholder="Enter your Last Name" required>
                                        </div>

                                        <!-- Gender -->

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
            
                                        <!-- Email -->
                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                                        </div>

                                        <!-- Mobile Number -->
                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label">Mobile Number</label>
                                            <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Enter your mobile number" 
                                                pattern="0[0-9]{10}" required oninput="formatMobile()">
                                            <small class="form-text text-muted">Please enter an 11-digit mobile number</small>
                                        </div>

                                        <!-- House No./Street -->
                                        <div class="col-md-4">
                                            <label for="houseStreet" class="form-label">House No./Blk or Lot No., Street</label>
                                            <input type="text" class="form-control" name="house_street" id="houseStreet" placeholder="e.g., Blk 5 Lot 3, Sampaguita St." required>
                                        </div>
            
                                        <!-- Barangay -->
                                        <div class="col-md-4">
                                            <label for="barangay" class="form-label">Barangay</label>
                                            <input type="text" class="form-control" name="barangay" id="barangay" placeholder="e.g., Barangay San Isidro" required>
                                        </div>
            
                                        <!-- City/Municipality -->
                                        <div class="col-md-4">
                                            <label for="city" class="form-label">City/Municipality</label>
                                            <input type="text" class="form-control" name="city" id="city" placeholder="e.g., Quezon City" required>
                                        </div>
            
                                        <!-- Province -->
                                        <div class="col-md-6">
                                            <label for="province" class="form-label">Province</label>
                                            <input type="text" class="form-control" name="province" id="province" placeholder="e.g., Metro Manila" required>
                                        </div>
            
                                        <!-- Postal Code -->
                                        <div class="col-md-6">
                                            <label for="postalCode" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control" name="postal_code" id="postalCode" placeholder="e.g., 1100" required>
                                        </div>
            
                                        <!-- Password -->
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" minlength="8" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-md-6">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="Re-enter your password" minlength="8" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', this)">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Password Mismatch Error Message -->
                                        <div class="col-12">
                                            <div id="passwordError" class="text-danger text-center" style="display: none;">
                                                Passwords do not match.
                                            </div>
                                        </div>
            
                                        <!-- Submit Button -->
                                        <div class="col-12 d-grid mt-3">
                                            <button type="submit" class="custom-btn btn btn-primary" name="submit">Register</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
            
                            <!-- Sign In Link -->
                            <div class="card-footer text-center">
                                Already have an account? <a href="index.php" class="text-primary">Sign In</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    
    
    <div class="footer">
        &copy; 2025 Your Website | All Rights Reserved
    </div>


    <!-- Bootstrap JS -->

        <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorDiv = document.getElementById('passwordError');
        
            if (password !== confirmPassword) {
                errorDiv.style.display = 'block'; // show error
                event.preventDefault(); // prevent form submission
            } else {
                errorDiv.style.display = 'none'; // hide error if fixed
            }
        });
        </script>
        <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
            } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
            }
        }
        </script>
        
        <script>
    function formatMobile() {
    const mobileInput = document.getElementById("mobile");
    let mobileValue = mobileInput.value;

    // Remove all non-numeric characters
    mobileValue = mobileValue.replace(/\D/g, '');

    // Limit to 11 digits
    if (mobileValue.length > 11) {
        mobileValue = mobileValue.slice(0, 11);
    }

    // Set the cleaned value back (without formatting)
    mobileInput.value = mobileValue;
}
</script>

    <?php } ?>
</body>
</html>