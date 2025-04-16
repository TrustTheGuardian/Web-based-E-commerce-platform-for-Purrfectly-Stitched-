<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>

        <!-- Bootstrap CDN
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     -->
        <!-- Bootstrap Icons CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

        <!-- css link -->
        <link rel="stylesheet" href="css_files/admin_users_styles.css">

</head>
<body>

    <div class="container">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                <img src="pictures/Purrfectly Stitch.png" alt="Purrfectly Stitched Logo">
                </div>
            
                <div class="close" id="close-btn">
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>

            <div class="sidebar">
                <a href="admin_dashboard.html" class="">
                    <i class="bi bi-grid-fill"></i>
                    <h3>Dashboard</h3>
                </a>
                <a href="admin_users.php" class="">
                    <i class="bi bi-people-fill"></i>
                    <h3>Users</h3>
                </a>
                <a href="admin_inventory_v2.php" class="">
                    <i class="bi bi-box2-heart-fill"></i>
                    <h3>Products</h3>
                </a>
                <a href="" class="">
                    <i class="bi bi-bag-check-fill"></i>
                    <h3>Orders</h3>
                </a>
                <a href="" class=""> 
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <h3>Reports</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside / side bar -->

        <main>
            <h1>Manage Users</h1>
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="text">
                <button class="btn-search">Search</button>
            </div>

            <!-- Users Table -->
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>User ID</th>
                            <th>Full name</th>
                            <th>Mobile Number</th>
                            <th>Address</th>
                            <th>Account Created</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            include 'db_connection.php';

                            $query = "SELECT * FROM users";
                            $result = mysqli_query($con, $query);

                            while($row = mysqli_fetch_assoc($result)) {
                                $id = $row['user_ID'];
                                $name = $row['FirstName'] . ' ' . $row['LastName'];
                                $mobile = $row['Mobile'];
                                $address = $row['Address'];
                                $created = date("m/d/Y", strtotime($row['CreatedAt']));
                                $status = $row['status'];

                                echo "<tr>
                                    <td><i class='bi bi-person-fill user-icon'></i></td>
                                    <td>$id</td>
                                    <td>$name</td>
                                    <td>$mobile</td>
                                    <td>$address</td>
                                    <td>$created</td>
                                    <td>$status</td>
                                    <td class='actions'>
                                        <a href='admin_userprofile.php?user_ID=$id' class='action-link view'>View</a>
                                        <a href='admindelete_user.php?user_ID=$id' class='action-link delete'>Delete</a> |";

                                if($status == 'active') {
                                    echo "<a href='adminban_user.php?user_ID=$id' class='action-link ban'>Ban</a>
                                        <span class='action-link unban' style='display:none;'>Unban</span>";
                                } else {
                                    echo "<a href='adminunban_user.php?user_ID=$id' class='action-link unban'>Unban</a>
                                        <span class='action-link ban' style='display:none;'>Ban</span>";
                                }

                                echo "</td></tr>";
                            }
                        ?>
                    </tbody>

                    
                </table>
            </div>

        </main>
        <!-- END OF MAIN  -->

         <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <i class="bi bi-list"></i>
                </button>
                <div class="theme-toggler">
                    <i class="bi bi-brightness-high-fill active"></i>
                    <i class="bi bi-moon-fill"></i>
                </div>
                <div class="log-out">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <h3>Log Out</h3>
                </div>
            </div>
            <!-- END OF TOP AREA -->

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>

        // Get current page URL (excluding query strings and hashes)
        const currentPage = window.location.pathname.split('/').pop();

        // Get all sidebar links
        const sidebarLinks = document.querySelectorAll('.sidebar a');

        sidebarLinks.forEach(link => {
            // Extract the filename from the href attribute
            const linkPage = link.getAttribute('href').split('/').pop();

            // If it matches the current page, add 'active' class
            if (linkPage === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        const sideMenu = document.querySelector("aside");
        const menuBtn = document.querySelector("#menu-btn");
        const closeBtn = document.querySelector("#close-btn")
        const themeToggler = document.querySelector(".theme-toggler")
    
        //show sidebar
        menuBtn.addEventListener('click', () => {
            sideMenu.style.display = 'block';
        })
    
        //close sidebar
        closeBtn.addEventListener('click', () => {
            sideMenu.style.display = 'none';
        })
    
        //change theme
        themeToggler.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme-variables')
    
            themeToggler.querySelector('i:nth-child(1)').classList.toggle('active');
            themeToggler.querySelector('i:nth-child(2)').classList.toggle('active');
        })
    
        // Toggle Ban and Unban
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.querySelector('.users-table');
    
            table.addEventListener('click', function (e) {
                if (e.target.classList.contains('ban')) {
                    const ban = e.target;
                    const unban = ban.parentElement.querySelector('.unban');
                    ban.style.display = 'none';
                    unban.style.display = 'inline';
                }
    
                if (e.target.classList.contains('unban')) {
                    const unban = e.target;
                    const ban = unban.parentElement.querySelector('.ban');
                    unban.style.display = 'none';
                    ban.style.display = 'inline';
                }
            });
        });
    </script>
    

</body>
</html>