<?php 
   session_start();
   include("php/config.php");

   if(isset($_POST['submit'])){
       // Escape user inputs to prevent SQL Injection
       $username = mysqli_real_escape_string($con, $_POST['username']);
       $password = mysqli_real_escape_string($con, $_POST['password']);

       // Query the database to check credentials
       $result = mysqli_query($con, "SELECT * FROM users WHERE Username='$username'AND Password='$password' ") or die("Query Failed");

       // Check if the user exists before fetching data
       if(mysqli_num_rows($result) > 0){
           $row = mysqli_fetch_assoc($result);

           // Ensure 'Password' key exists before verifying
           if(isset($row['Password']) && password_verify($password, $row['Password'])){
               // Store user information in session variables
               $_SESSION['valid'] = $row['Email'];
               $_SESSION['username'] = $row['Username'];
               $_SESSION['age'] = $row['Age'];
               $_SESSION['id'] = $row['Id'];

               // Redirect to home page after successful login
               header("Location: home.html");
               exit; // Ensure script stops execution after redirection
           } else {
               echo "<div class='message'>
                        <p>Wrong Username or Password</p>
                    </div> <br>";
               echo "<a href='index.php'><button class='btn btn-primary'>Go Back</button></a>";
           }
       } else {
           echo "<div class='message'>
                    <p>Wrong Username or Password</p>
                </div> <br>";
           echo "<a href='index.php'><button class='btn btn-primary'>Go Back</button></a>";
       }
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS Style -->
    <link rel="stylesheet" href="css_files/index.css">
</head>

<!-- Overall Layout -->
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <!-- Container -->
    <div class="container">
        <!-- Row For Centering -->
        <div class="row justify-content-center">
            <!-- Column to Adjust Form -->
            <div class="col-md-6">
                <!-- Card Component -->
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header text-center text-white">
                        <h4>Login</h4>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <form action="" method="post">
                            <!-- Username Input  -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <!-- Password Input -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <!-- Remember me -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options" id="remember" value="remember">
                                <label class="form-check-label" for="remember">
                                    Remember Me 
                                    <a href="forgotpassword.html" class="text-primary">Forgot password?</a>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" name="submit" class="custom-btn">Login</button>
                            </div>
                        </form>
                    </div>

                    <!-- Sign Up -->
                    <div class="card-footer text-center">
                        Don't have an account? <a href="registration.php" class="text-primary">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>