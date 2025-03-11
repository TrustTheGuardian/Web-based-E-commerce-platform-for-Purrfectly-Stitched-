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
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <?php 
                include("php/config.php");
                if(isset($_POST['submit'])){
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $address = $_POST['address'];
                    $password = $_POST['password'];

                    // Verifying if the email is unique
                    $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

                    if(mysqli_num_rows($verify_query) != 0) {
                        echo "<div class='message'>
                                <p>This email is already in use. Try another one.</p>
                              </div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                    } else {
                        // Hash the password before storing
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        mysqli_query($con, "INSERT INTO users (Username, Email, Address, Password) VALUES ('$username', '$email', '$address', '$hashed_password')") 
                        or die("Error Occurred");

                        echo "<div class='message'>
                                <p>Registration successful!</p>
                              </div> <br>";
                        echo "<a href='index.php'><button class='btn'>Login Now</button></a>";
                    }
                } else {
            ?>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header text-center text-white">
                        <h4>Register</h4>
                    </div>

                    <div class="card-body">
                        <form action="" method="post">
                            <!-- Username Input -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required>
                            </div>

                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                            </div>

                            <!-- Address Input -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="Enter your address" required>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="custom-btn" name="submit">Sign Up</button>
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


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php } ?>
</body>
</html>