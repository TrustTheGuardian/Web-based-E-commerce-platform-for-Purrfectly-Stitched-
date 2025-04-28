<?php
// Include database connection file
include('db_connection.php');

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $product_title = trim($_POST['product_title']);
    $product_description = trim($_POST['product_description']);
    $product_price = floatval($_POST['product_price']);
    $product_quantity = intval($_POST['product_quantity']);
    $product_category_id = intval($_POST['product_category_id']);
    $product_status = 'active'; // You can change this based on logic

    // Prepare insert query for product
    $stmt = $con->prepare("INSERT INTO products (product_title, product_description, product_price, product_quantity, product_status, product_category_ID) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisi", $product_title, $product_description, $product_price, $product_quantity, $product_status, $product_category_id);

    if ($stmt->execute()) {
        // Get the newly inserted product ID
        $product_ID = $stmt->insert_id;
        $stmt->close();

        // Upload path
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Handle multiple image uploads
        if (isset($_FILES['product_images'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            // Loop through all uploaded files
            for ($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
                $fileName = basename($_FILES['product_images']['name'][$i]);
                $fileType = $_FILES['product_images']['type'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                $error = $_FILES['product_images']['error'][$i];
                $tmp_name = $_FILES['product_images']['tmp_name'][$i];

                // Validate the file
                if ($error === UPLOAD_ERR_OK && in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                    $uniqueName = uniqid() . "_" . $fileName;
                    $targetFilePath = $targetDir . $uniqueName;

                    // Move the file to the upload directory
                    if (move_uploaded_file($tmp_name, $targetFilePath)) {
                        // Insert image path into the database
                        $img_stmt = $con->prepare("INSERT INTO product_images (product_ID, image_path) VALUES (?, ?)");
                        $img_stmt->bind_param("is", $product_ID, $targetFilePath);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }

        echo "New product and images added successfully.";
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- css link -->
    <link rel="stylesheet" href="css_files/admin_addedit_styles.css">
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
        <h1>Add Product</h1>

        <!-- PRODUCT FORM -->
        <form action="admin_add_product.php" method="POST" enctype="multipart/form-data">
            <div class="product-description">
                <!-- Image Upload Section -->
                <div class="add-images">
                    <div class="dp">
                        <div class="carousel">
                            <div class="carousel-images" id="carouselImages">
                                <div class="image-wrapper">
                                    <img src="pictures/Purrfectly Stitch.png" alt="Product Image">
                                    <span class="remove-btn">&times;</span>
                                </div>
                            </div>
                            <button type="button" class="carousel-btn prev">&lt;</button>
                            <button type="button" class="carousel-btn next">&gt;</button>
                        </div>
                    </div>

                    <!-- Add Images Button -->
                    <button type="button" id="addImagesBtn">Add Images</button>
                    <input type="file" id="imageInput" name="product_images[]" accept="image/*" multiple hidden>
                </div>

                <!-- Product Details Section -->
                <div class="product-details">
                    <!-- Product Title -->
                    <div class="form-group">
                        <label for="product_title"><strong>Product Title:</strong></label>
                        <input type="text" id="product_title" name="product_title" class="styled-input" required>
                    </div>

                    <!-- Product Description -->
                    <div class="form-group">
                        <label for="description"><strong>Product Description:</strong></label>
                        <textarea id="description" name="product_description" class="styled-input" required></textarea>
                    </div>

                    <!-- Product Price -->
                    <div class="form-group">
                        <label for="price"><strong>Product Price:</strong> ₱</label>
                        <input type="number" id="price" name="product_price" class="styled-input" step="0.01" required>
                    </div>

                    <!-- Product Quantity -->
                    <div class="form-group">
                        <label for="quantity"><strong>Product Quantity:</strong></label>
                        <input type="number" id="quantity" name="product_quantity" class="styled-input" required>
                    </div>

                    <!-- Product Category -->
                    <div class="form-group">
                        <label for="category"><strong>Category:</strong></label>
                        <div class="custom-select-wrapper">
                            <select id="category" name="product_category_id" class="custom-select" required>
                                <option value="" disabled selected>Select a category</option>
                                <?php
                                    // Fetch categories from the database
                                    $category_sql = "SELECT * FROM product_category";
                                    $category_result = mysqli_query($con, $category_sql);
                                    while ($row = mysqli_fetch_assoc($category_result)) {
                                        echo "<option value='{$row['product_category_ID']}'>{$row['category_name']}</option>";
                                    }
                                ?>
                            </select>
                            <i class="bi bi-chevron-down custom-icon"></i>
                        </div>
                    </div>
                    <div class="category-actions">
                        <span class="action-link add-category">Add Category</span> |
                        <span class="action-link delete">Delete Category</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="actions">
                <button type="submit" class="action-link add">Add</button>
                <a href="admin_inventory_v2.php" class="action-link cancel">Cancel</a>
            </div>
        </form>
    </main>


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
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="close-modal" data-modal="addCategoryModal">&times;</span>
            <h2>Add Category</h2>
            <input type="text" id="newCategoryInput" placeholder="Enter new category name" class="styled-input">
            <button id="confirmAddCategory" class="modal-button">Add</button>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div id="deleteCategoryModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="close-modal" data-modal="deleteCategoryModal">&times;</span>
            <h2>Delete Category</h2>
            <select id="deleteCategorySelect" class="styled-input">
                <!-- JS will populate this -->
            </select>
            <button id="confirmDeleteCategory" class="modal-button">Delete</button>
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
            const actions = document.querySelector('.actions');
    
            actions.addEventListener('click', function (e) {
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
    
        // IMAGE CAROUSEL WITH ADD & REMOVE
        let currentIndex = 0;
        const carouselImages = document.querySelector('.carousel-images');
        const imageInput = document.getElementById('imageInput');
        const addImagesBtn = document.getElementById('addImagesBtn');
        const prevBtn = document.querySelector('.carousel-btn.prev');
        const nextBtn = document.querySelector('.carousel-btn.next');

        // Open file input when "Add Images" button is clicked
        addImagesBtn.addEventListener('click', () => {
            imageInput.click(); // Trigger hidden input
        });

        // Handle image selection and preview
        imageInput.addEventListener('change', function () {
            const files = Array.from(this.files);
            files.forEach(file => {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('image-wrapper');
            
                        const img = document.createElement('img');
                        img.src = e.target.result;
            
                        const removeBtn = document.createElement('span');
                        removeBtn.classList.add('remove-btn');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.onclick = () => {
                            wrapper.remove();
                            updateIndexOnRemove();
                            updateCarousel();
                        };
            
                        wrapper.appendChild(img);
                        wrapper.appendChild(removeBtn);
                        carouselImages.appendChild(wrapper);
            
                        currentIndex = carouselImages.children.length - 1;
                        updateCarousel();
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // IMAGE CAROUSEL PREV & NEXT
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < carouselImages.children.length - 1) {
                currentIndex++;
                updateCarousel();
            }
        });

        // Update carousel position based on current index
        function updateCarousel() {
            const offset = currentIndex * 350; // Adjust width per image including margin
            carouselImages.style.transform = `translateX(-${offset}px)`;
        }

        // Update the index when images are removed
        function updateIndexOnRemove() {
            if (currentIndex >= carouselImages.children.length) {
                currentIndex = carouselImages.children.length - 1;
            }
        }

            // Show/Hide Modals
        const addCategoryBtn = document.querySelector('.add-category');
        const deleteCategoryBtn = document.querySelector('.delete');
        const addCategoryModal = document.getElementById('addCategoryModal');
        const deleteCategoryModal = document.getElementById('deleteCategoryModal');
        const closeModalBtns = document.querySelectorAll('.close-modal');

        addCategoryBtn.addEventListener('click', () => {
            addCategoryModal.style.display = 'block';
        });

        deleteCategoryBtn.addEventListener('click', () => {
            const select = document.getElementById('deleteCategorySelect');
            const currentOptions = document.querySelectorAll('#category option:not([disabled])');
            select.innerHTML = '';
            currentOptions.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.textContent;
                select.appendChild(option);
            });
            deleteCategoryModal.style.display = 'block';
        });

        </script>

        <script>

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById(btn.dataset.modal).style.display = 'none';
            });
        });

        window.addEventListener('click', e => {
            if (e.target.classList.contains('custom-modal')) {
                e.target.style.display = 'none';
            }
        });

        // Add Category Logic
        document.getElementById('confirmAddCategory').addEventListener('click', () => {
        const input = document.getElementById('newCategoryInput');
        const categoryName = input.value.trim();

        if (categoryName) {
            fetch('admin_add_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'category_name=' + encodeURIComponent(categoryName),
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    const categorySelect = document.getElementById('category');
                    const newOption = document.createElement('option');
                    newOption.value = categoryName;
                    newOption.textContent = categoryName;
                    categorySelect.appendChild(newOption);
                    input.value = '';
                    addCategoryModal.style.display = 'none';
                } else {
                    alert('Error: ' + data);
                }
            });
        } else {
            alert('Please enter a category name.');
        }
    });

        // Delete Category Logic
        document.getElementById('confirmDeleteCategory').addEventListener('click', () => {
            const deleteSelect = document.getElementById('deleteCategorySelect');
            const categoryId = deleteSelect.value;

            if (!categoryId) {
                alert("Please select a category to delete.");
                return;
            }

            // AJAX request to delete the category
            const formData = new FormData();
            formData.append('category_id', categoryId);

            fetch('admin_delete_category.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    // Remove from category dropdown
                    const categorySelect = document.getElementById('category');
                    const optionToRemove = categorySelect.querySelector(`option[value="${categoryId}"]`);
                    if (optionToRemove) categorySelect.removeChild(optionToRemove);

                    deleteSelect.querySelector(`option[value="${categoryId}"]`).remove();
                    deleteCategoryModal.style.display = 'none';
                    alert("Category deleted successfully.");
                } else if (data.trim() === "in_use") {
                    alert("This category is still in use by products and cannot be deleted.");
                } else {
                    alert("An error occurred while deleting the category.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Something went wrong.");
            });
        });

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

        </script>

    
</body>
</html>