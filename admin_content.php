<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

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
                <a href="admin_content.html" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside / side bar -->

        <main>
            <h1>Content Management</h1>
            <p>Edit Banners and Announcement</p><br>

            <!-- Banner Management Section -->
            <div class="carousel-container">
                <h2>Banner-Content</h2>
                <div class="main-image">
                <img id="currentImage" src="pictures/Purrfectly Stitch.png" alt="Main Image">
                </div>
                <div class="thumbnails">
                <img src="pictures/Purrfectly Stitch.png" alt="Thumbnail 1" onclick="changeImage(this)">
                <img src="pictures/man-user-circle-icon.png" alt="Thumbnail 2" onclick="changeImage(this)">
                <img src="pictures/joshuam.png" alt="Thumbnail 3" onclick="changeImage(this)">
                </div>

                <!-- Add Banner Form -->
                <div class="action-image-container">
                <form action="upload_banner.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="banner_image" accept="image/*" required>
                    <button type="submit" class="add-banner">Add Image</button>
                </form>

                <!-- Example: Remove Banner Button (implement deletion logic if needed) -->
                <form action="delete_banner.php" method="POST">
                    <input type="hidden" name="banner_id" value="/* dynamically loaded banner ID */">
                    <button type="submit" class="remove-banner">Remove Image</button>
                </form>
                </div>
            </div>

            <!-- Announcement Management Section -->
            <div class="carousel-container">
                <h2>Announcement-Content</h2>

                <!-- Announcement Form -->
                <form action="post_announcement.php" method="POST" style="padding: 1rem;">
                <input type="text" name="title" placeholder="Announcement Title" required><br><br>
                <input type="date" name="announcement_date" required><br><br>
                <textarea name="content" placeholder="Write your announcement..." rows="5" style="width: 100%;" required></textarea><br><br>
                <button type="submit" class="add-announcement">Post Announcement</button>
                </form>

                <!-- List of Posted Announcements -->
                <div class="announcement-list" style="padding: 1rem;">
                <?php
                    include 'db_connection.php';
                    $res = mysqli_query($con, "SELECT * FROM announcements ORDER BY announcement_date DESC");
                    while ($row = mysqli_fetch_assoc($res)) {
                    echo "<div class='announcement-card'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='announcement-date'>" . date("F j, Y", strtotime($row['announcement_date'])) . "</p>";
                    echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
                    echo "</div><hr>";
                    }
                ?>
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

    <!-- Remove image Confirmation Modal -->
    <div id="removeBanner" class="custom-modal">
        <div class="custom-modal-content">
            <p>Are you sure you want to remove this Banner?</p>
            <div class="custom-modal-buttons">
                <button id="confirmremove">Yes</button>
                <button id="cancelremove">No</button>
            </div>
        </div>
    </div>

    <!-- Remove image Confirmation Modal -->
    <div id="removeAnnouncement" class="custom-modal">
        <div class="custom-modal-content">
            <p>Are you sure you want to remove this Announcement?</p>
            <div class="custom-modal-buttons">
                <button id="confirmremove">Yes</button>
                <button id="cancelremove">No</button>
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

        // carousel function
        function changeImage(thumbnail) {
        const mainImage = document.getElementById('currentImage');
        mainImage.src = thumbnail.src;
        }

        // --- REMOVE BANNER MODAL LOGIC ---
        const removeBannerBtn = document.querySelector('.remove-banner');
        const removeBannerModal = document.getElementById('removeBanner');
        const confirmRemoveBanner = document.querySelector('#removeBanner #confirmremove');
        const cancelRemoveBanner = document.querySelector('#removeBanner #cancelremove');

        removeBannerBtn.addEventListener('click', () => {
            removeBannerModal.style.display = 'block';
        });

        cancelRemoveBanner.addEventListener('click', () => {
            removeBannerModal.style.display = 'none';
        });

        confirmRemoveBanner.addEventListener('click', () => {
            const mainImage = document.querySelector('.carousel-container .main-image img');
            if (mainImage) {
                mainImage.src = '';
            }
            removeBannerModal.style.display = 'none';
        });

        // --- REMOVE ANNOUNCEMENT MODAL LOGIC ---
        const removeAnnouncementBtn = document.querySelector('.remove-announcement');
        const removeAnnouncementModal = document.getElementById('removeAnnouncement');
        const confirmRemoveAnnouncement = document.querySelector('#removeAnnouncement #confirmremove');
        const cancelRemoveAnnouncement = document.querySelector('#removeAnnouncement #cancelremove');

        removeAnnouncementBtn.addEventListener('click', () => {
            removeAnnouncementModal.style.display = 'block';
        });

        cancelRemoveAnnouncement.addEventListener('click', () => {
            removeAnnouncementModal.style.display = 'none';
        });

        confirmRemoveAnnouncement.addEventListener('click', () => {
            const mainImage = document.querySelectorAll('.carousel-container')[1].querySelector('.main-image img');
            if (mainImage) {
                mainImage.src = '';
            }
            removeAnnouncementModal.style.display = 'none';
        });

        // Close modals if clicking outside
        window.addEventListener('click', (event) => {
            if (event.target === removeBannerModal) {
                removeBannerModal.style.display = 'none';
            }
            if (event.target === removeAnnouncementModal) {
                removeAnnouncementModal.style.display = 'none';
            }
        });

    </script>

</body>
</html>