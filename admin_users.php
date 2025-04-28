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
                <a href="admin_dashboard.php" class="">
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
                <a href="admin_orders.php" class="">
                    <i class="bi bi-bag-check-fill"></i>
                    <h3>Orders</h3>
                </a>
                <a href="admin_reports.php" class=""> 
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <h3>Reports</h3>
                </a>
                <a href="admin_content.html" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
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
                            <th>Actions</th>
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
                                $isBanned = $row['is_banned'];

                                echo "<tr>
                                    <td><i class='bi bi-person-fill user-icon'></i></td>
                                    <td>$id</td>
                                    <td>$name</td>
                                    <td>$mobile</td>
                                    <td>$address</td>
                                    <td>$created</td>
                                    <td class='actions'>
                                        <a href='admin_userprofile.php?user_ID=$id' class='action-link view'>View</a> |
                                         <a href='#' onclick='openDeleteModal($id)' class='action-link delete'>Delete</a> |";

                                        if ($isBanned == 0) {
                                            // Not banned — show Ban
                                            echo "<a href='adminban_user.php?user_ID=$id' class='action-link ban'>Ban</a>
                                                  <span class='action-link unban' style='display:none;'>Unban</span>";
                                        } else {
                                            // Banned — show Unban
                                            echo "<a href='adminban_user.php?user_ID=$id' class='action-link unban'>Unban</a>
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

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="custom-modal">
        <div class="custom-modal-content">
            <p>Are you sure you want to log out?</p>
            <div class="custom-modal-buttons">
                <button id="confirmLogout">Yes</button>
                <button id="cancelLogout">No</button>
            </div>
        </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <p>Are you sure you want to delete this user?</p>
            <div class="custom-modal-buttons">
                <button id="confirmDelete">Yes</button>
                <button id="cancelDelete">No</button>
            </div>
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
    const closeBtn = document.querySelector("#close-btn");
    const themeToggler = document.querySelector(".theme-toggler");

    // Show sidebar
    menuBtn.addEventListener('click', () => {
        sideMenu.style.display = 'block';
    });

    // Close sidebar
    closeBtn.addEventListener('click', () => {
        sideMenu.style.display = 'none';
    });

    // Change theme
    themeToggler.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme-variables');

        themeToggler.querySelector('i:nth-child(1)').classList.toggle('active');
        themeToggler.querySelector('i:nth-child(2)').classList.toggle('active');
    });

    // Toggle Ban and Unban
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector('.users-table');

        if (table) {
            table.addEventListener('click', function (e) {
                if (e.target.classList.contains('ban')) {
                    const ban = e.target;
                    const unban = ban.parentElement.querySelector('.unban');
                    ban.style.display = 'none';
                    if (unban) unban.style.display = 'inline';
                }

                if (e.target.classList.contains('unban')) {
                    const unban = e.target;
                    const ban = unban.parentElement.querySelector('.ban');
                    unban.style.display = 'none';
                    if (ban) ban.style.display = 'inline';
                }
            });
        }
    });

    // Logout modal logic
    const logoutBtn = document.querySelector('.log-out');
    const logoutModal = document.getElementById('logoutModal');
    const confirmLogout = document.getElementById('confirmLogout');
    const cancelLogout = document.getElementById('cancelLogout');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'flex'; // use 'flex' instead of 'block' to center it
        });
    }

    if (cancelLogout) {
        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });
    }

    if (confirmLogout) {
        confirmLogout.addEventListener('click', () => {
            window.location.href = "logout.php"; // Update as needed
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === logoutModal) {
            logoutModal.style.display = 'none';
        }
    });

    // Delete user modal logic
    let deleteUserId = null;

    // Open modal and save the user ID
    function openDeleteModal(userId) {
        deleteUserId = userId;
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.style.display = 'flex'; // use flex to center
        }
    }

    // Close the delete modal
    function closeDeleteModal() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.style.display = 'none';
        }
        deleteUserId = null;
    }

    // Confirm delete function
    function confirmDelete() {
        if (deleteUserId !== null) {
            window.location.href = `admindelete_user.php?user_ID=${deleteUserId}`;
        }
    }

    // Attach event listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const cancelDeleteBtn = document.getElementById('cancelDelete');

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', confirmDelete);
        }

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        }

        // Optional: close modal when clicking outside the content
        window.addEventListener('click', (event) => {
            const deleteModal = document.getElementById('deleteModal');
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
    });

</script>
    

</body>
</html>