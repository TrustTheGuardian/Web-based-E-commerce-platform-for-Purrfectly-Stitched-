<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- css link -->
    <link rel="stylesheet" href="css_files/adminstyles.css">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <!-- Menu bar -->
    <?php include 'admin_menu.php'; ?>

    <!-- main display -->
    <section id="interface">
    <?php include 'admin_topbar.php'; ?>

        <h3 class="i-name">Dashboard</h3>

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-3">
                        <span>Placeholder</span>
                    <div class="w-100 h-50 p-5">
                        <h1 class="text-center">$</h1>
                        <p class="text-center text-success">%Placeholder</p>
                        <p class="text-center">Placeholder</p>
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h3>Our Services</h3>
                        <p>Discover the services we offer.</p>
                        <a href="#" class="btn btn-primary">Explore</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h3>Contact Us</h3>
                        <p>Get in touch with us today.</p>
                        <a href="contact.html" class="btn btn-primary">Contact</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </section>

    
        <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>