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

    <div>
        <div id="cards" class="row justify-content-around mt-1">
            <div class="card card-hw rounded bg white custom-shadow col-lg-3 col-xs-3 col-md-3 col-sm-12 mb-3 mb-md-0">
                <div class="card-body p-1">
                    <span>Placeholder</span>
                    <div class="w-100 h-50 p-5">
                        <h1 class="text-center">$</h1>
                        <p class="text-center text-success">%Placeholder</p>
                        <p class="text-center">Placeholder</p>
                    </div>
                </div>
            </div>
            <div class="card card-hw rounded bg white custom-shadow col-lg-3 col-xs-3 col-md-3 col-sm-12 mb-3 mb-md-0">
                <div class="card-body p-1">
                    <span>Placeholder</span>
                    <div class="w-100 h-50 p-5">
                        <h1 class="text-center">$</h1>
                        <p class="text-center text-success">%Placeholder</p>
                        <p class="text-center">Placeholder</p>
                    </div>
                </div>
            </div>
            <div class="card card-hw rounded bg white custom-shadow col-lg-3 col-xs-3 col-md-3 col-sm-12 mb-3 mb-md-0">
                <div class="card-body p-1">
                    <span>Placeholder</span>
                    <div class="w-100 h-50 p-5">
                        <h1 class="text-center">$</h1>
                        <p class="text-center text-success">%Placeholder</p>
                        <p class="text-center">Placeholder</p>
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