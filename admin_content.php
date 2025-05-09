<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?unauthorized=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content</title>

        <!-- Bootstrap CDN
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     -->
        <!-- Bootstrap Icons CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

        <!-- css link -->
        <link rel="stylesheet" href="css_files/admin_content_styles.css">

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
                <a href="admin_content.php" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside / side bar -->

        <main>
            <h1>Content Management</h1>
            <p>Edit Banners and Announcement</p><br>

            <?php
                include 'db_connection.php';

                $sql = "SELECT * FROM banners WHERE status = 'active' ORDER BY created_at DESC";
                $result = mysqli_query($con, $sql);
                ?>

                <!-- Banner Management Section -->
                <div class="banner-wrapper">
                    <!-- Left: Add Banner -->
                    <div class="postBanner">
                        <h2>Upload Banner</h2>
                        <form action="upload_banner.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="banner_image" accept="image/*" required>
                            <button type="submit" class="add-banner"><i class="bi bi-upload"></i> Add Image</button>
                        </form>
                    </div>
                    
                    <!-- Right: Posted Banners -->
                    <div class="listBanner">
                        <h2>Posted Banners</h2>
                        <div class="banner-scroll">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <div class="banner-card">
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Banner">
                                    <form action="delete_banner.php" method="POST">
                                        <input type="hidden" name="banner_id" value="<?php echo $row['banner_ID']; ?>">
                                        <button type="submit" class="delete-banner"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>     

                <?php
                    include 'db_connection.php';

                    $announcements = mysqli_query($con, "SELECT * FROM announcements ORDER BY announcement_date DESC");
                    ?>

                    <!-- Announcement Management Section -->
                        <div class="announcement-container">
                            <div class="announcement-wrapper">
                                <!-- Post Announcement Form -->
                                <div class="postAnnouncement">
                                    <h2>Post Announcement</h2>
                                    <form action="post_announcement.php" method="POST">
                                        <input type="text" name="title" placeholder="Announcement Title" required>
                                        <input type="date" name="announcement_date" required>
                                        <textarea name="content" placeholder="Write your announcement here" required></textarea>
                                        <button type="submit" class="add-announcement">Post Announcement</button>
                                    </form>
                                </div>

                                <!-- Display Announcements -->
                                <div class="listAnnouncement">
                                    <h2>Posted Announcements</h2>

                                    <?php
                                    include 'db_connection.php';
                                    $res = mysqli_query($con, "SELECT * FROM announcements ORDER BY announcement_date DESC");
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo '<div class="announcement_card">';
                                        echo '<form class="delete-announcement-form" method="POST" action="delete_announcement.php">';
                                        echo '<input type="hidden" name="announcement_id" value="' . $row['announcement_ID'] . '">';
                                        echo '<button type="button" class="delete-announcement"><i class="bi bi-trash-fill"></i></button>';
                                        echo '</form>';
                                        echo '<strong>' . htmlspecialchars($row['title']) . '</strong>';
                                        echo '<small>' . htmlspecialchars(date("m/d/Y", strtotime($row['announcement_date']))) . '</small>';
                                        echo '<p>' . nl2br(htmlspecialchars($row['content'])) . '</p>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
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

    <!-- Delete Banner Modal -->
    <div id="bannerModal" class="modal-overlay">
        <div class="modal-box">
        <h2>Delete Banner</h2>
        <p>Are you sure you want to delete this banner?</p>
        <form id="bannerDeleteForm" method="POST" action="delete_banner.php">
            <input type="hidden" name="banner_id" id="modalBannerId">
            <div class="modal-buttons">
            <button type="button" class="cancel-btn" onclick="closeBannerModal()">Cancel</button>
            <button type="submit" class="confirm-btn">Delete</button>
            </div>
        </form>
        </div>
    </div>
    
    <!-- Delete Announcement Modal -->
<div id="announcementModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h2>Delete Announcement</h2>
        <p>Are you sure you want to delete this announcement?</p>
        <div class="modal-buttons">
            <button type="button" class="cancel-btn" onclick="closeAnnouncementModal()">Cancel</button>
            <button type="button" class="confirm-btn" id="confirmDeleteAnnouncement">Delete</button>
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

        // Logout modal logic
        const logoutBtn = document.querySelector('.log-out');
        const logoutModal = document.getElementById('logoutModal');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');

        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'block';
        });

        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        confirmLogout.addEventListener('click', () => {
            window.location.href = "logout.php"; // Update as needed
        });

        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });

        // Banner Modal Logic
        const bannerModal = document.getElementById("bannerModal");
        const modalBannerId = document.getElementById("modalBannerId");
        const bannerDeleteForm = document.getElementById("bannerDeleteForm");

        document.querySelectorAll(".delete-banner").forEach(button => {
            button.addEventListener("click", function(e) {
            e.preventDefault();
            const form = this.closest("form");
            const bannerId = form.querySelector('input[name="banner_id"]').value;
            modalBannerId.value = bannerId;
            bannerModal.style.display = "flex";
            });
        });

        function closeBannerModal() {
            bannerModal.style.display = "none";
        }

         // Announcement Modal Logic
        const announcementModal = document.getElementById("announcementModal");
        let currentForm = null;

        document.querySelectorAll(".delete-announcement").forEach(button => {
            button.addEventListener("click", function () {
                currentForm = this.closest("form"); // reference to the correct form
                announcementModal.style.display = "flex";
            });
        });

        document.getElementById("confirmDeleteAnnouncement").addEventListener("click", function () {
            if (currentForm) {
                currentForm.submit(); // confirm and submit form
            }
            closeAnnouncementModal();
        });

        function closeAnnouncementModal() {
            announcementModal.style.display = "none";
            currentForm = null;
        }

    </script>

</body>
</html>