<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?unauthorized=1");
    exit;
}
include 'db_connection.php';

// Get the product ID from query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

// Fetch product details
if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE product_ID = $product_id";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_product'])) {
    $title = mysqli_real_escape_string($con, $_POST['product_title']);
    $desc = mysqli_real_escape_string($con, $_POST['product_description']);
    $price = floatval($_POST['product_price']);
    $quantity = intval($_POST['product_quantity']);
    $category = intval($_POST['product_category_ID']);

    $updateQuery = "UPDATE products SET 
                    product_title = '$title',
                    product_description = '$desc',
                    product_price = $price,
                    product_quantity = $quantity,
                    product_category_ID = $category
                    WHERE product_ID = $product_id";

    if (mysqli_query($con, $updateQuery)) {
        echo "<script>alert('Product updated successfully'); window.location.href='admin_inventory_v2.php';</script>";
    } else {
        echo "Error updating product: " . mysqli_error($con);
    }
}

// ✅ Fetch Reviews
$reviewQuery = "
    SELECT r.*, u.FirstName, u.LastName 
    FROM reviews r
    JOIN users u ON r.user_ID = u.user_ID
    WHERE r.product_ID = ?
    ORDER BY r.created_at DESC
";

$stmt = $con->prepare($reviewQuery);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>

        <!-- Bootstrap CDN
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     -->
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
                <a href="admin_content.php" class=""> 
                    <i class="bi bi-hdd-stack-fill"></i>
                    <h3>Banner & Cards</h3>
                </a>
            </div>
        </aside>
        <!-- end of aside / side bar -->

        <main>
            <h1>Manage Product</h1>

            <!-- ACCOUNT PROFILE AND DETAILS -->
            <div class="product-description">
                <div class="add-images">
                    <div class="dp">
                        <div class="carousel">
                            <div class="carousel-images" id="carouselImages">
                                <div class="image-wrapper">
                                    <img src="pictures/Purrfectly Stitch.png" alt="product Image">
                                    <span class="remove-btn">&times;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button id="addImagesBtn">Add Images</button>
                    <input type="file" id="imageInput" accept="image/*" multiple style="display: none;">                    
                    
                </div>
                <form method="POST" action="">
                    <div class="product-details">
                        <div class="form-group">
                            <label for="product_title"><strong>Product Title:</strong></label>
                            <input type="text" id="product_title" name="product_title" class="styled-input" 
                                value="<?php echo htmlspecialchars($product['product_title']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="description"><strong>Product Description:</strong></label>
                            <textarea id="description" name="product_description" class="styled-input"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="price"><strong>Product Price:</strong> ₱</label>
                            <input type="number" id="price" step="0.01" name="product_price" class="styled-input"
                                value="<?php echo htmlspecialchars($product['product_price']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="quantity"><strong>Product Quantity:</strong></label>
                            <input type="number" id="quantity" name="product_quantity" class="styled-input"
                                value="<?php echo htmlspecialchars($product['product_quantity']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="category"><strong>Category:</strong></label>
                            <div class="custom-select-wrapper">
                                <select id="category" name="product_category_ID" class="custom-select" required>
                                    <option value="" disabled>Select a category</option>
                                    <?php
                                    $catQuery = "SELECT * FROM product_category";
                                    $catResult = mysqli_query($con, $catQuery);
                                    while ($cat = mysqli_fetch_assoc($catResult)) {
                                        $selected = ($cat['product_category_ID'] == $product['product_category_ID']) ? 'selected' : '';
                                        echo "<option value='{$cat['product_category_ID']}' $selected>{$cat['category_name']}</option>";
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

                        <button type="submit" name="update_product" class="modal-button">Update Product</button>
                    </div>
                </form>
                
            </div>

            <div class="customer-reviews">
                    <h2>Customer Reviews and Comments</h2>
                    
                    <?php
                    $totalReviews = $reviews->num_rows;
                    $averageRating = 0;

                    if ($totalReviews > 0) {
                        $sumQuery = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_ID = $product_id";
                        $sumResult = mysqli_query($con, $sumQuery);
                        $avgRow = mysqli_fetch_assoc($sumResult);
                        $averageRating = round($avgRow['avg_rating'], 1);
                    }

                    $filledStars = str_repeat('<i class="bi bi-star-fill"></i>', floor($averageRating));
                    $halfStar = ($averageRating - floor($averageRating)) >= 0.5 ? '<i class="bi bi-star-half"></i>' : '';
                    $emptyStars = str_repeat('<i class="bi bi-star"></i>', 5 - ceil($averageRating));
                    ?>

                    <div class="star text-warning">
                        <?= $filledStars . $halfStar . $emptyStars ?>
                        <small class="text-muted">(<?= $totalReviews ?> reviews)</small>
                    </div>

                    <div class="comment" data-review-id="<?= $review['review_ID'] ?>">
                        <div id="reviewsContainer">
                            <?php if ($totalReviews > 0): ?>
                                <?php while ($review = $reviews->fetch_assoc()): 
                                    $name = htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']);
                                    $date = date('m/d/y', strtotime($review['created_at']));
                                    $rating = intval($review['rating']);
                                    $text = htmlspecialchars($review['review_text']);
                                    ?>
                                    <div class="comment">
                                        <div class="name-and-date">
                                            <strong><?= $name ?></strong>
                                            <small class="text-muted"><?= $date ?></small>
                                        </div>
                                        <div class="star text-warning">
                                            <?= str_repeat('<i class="bi bi-star-fill"></i>', $rating) ?>
                                            <?= str_repeat('<i class="bi bi-star"></i>', 5 - $rating) ?>
                                        </div>
                                        <p><?= $text ?></p>
                                        <div class="comment-action">
                                            <span class="action-link reply">reply</span> | 
                                            <span class="action-link remove" data-review-id="<?= $review['review_ID'] ?>">remove</span>
                                        </div>
                                        <div class="reply-box" style="display: none; margin-top: 0.5rem;">
                                            <input type="text" class="reply-input" placeholder="Type your reply..." />
                                            <button class="submit-reply">Post</button>
                                        </div>
                                        <?php if (!empty($review['admin_reply'])): ?>
                                            <div class="admin-reply" style="margin-top: 0.5rem;">
                                                <strong class="text-primary">Admin Reply:</strong> 
                                                <span class="admin-reply-text"><?= htmlspecialchars($review['admin_reply']) ?></span>
                                                <button class="edit-reply btn btn-sm btn-outline-primary">Edit</button>
                                            </div>
                                        <?php else: ?>
                                            <div class="admin-reply" style="margin-top: 0.5rem;">
                                                <span class="admin-reply-text"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No reviews yet for this product.</p>
                            <?php endif; ?>
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
    
        addImagesBtn.addEventListener('click', () => {
            imageInput.click(); // Trigger hidden input
        });
    
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
                            if (currentIndex >= carouselImages.children.length) {
                                currentIndex = carouselImages.children.length - 1;
                            }
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
        
        function updateCarousel() {
            const offset = currentIndex * 350; // Adjust width per image including margin
            carouselImages.style.transform = `translateX(-${offset}px)`;
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

    <script>
document.addEventListener('click', function (e) {
    // Toggle reply box visibility for replying
    if (e.target.classList.contains('reply')) {
        const replyBox = e.target.closest('.comment').querySelector('.reply-box');
        replyBox.style.display = replyBox.style.display === 'none' ? 'block' : 'none';
    }

    // Submit or Edit reply
    if (e.target.classList.contains('submit-reply')) {
        const commentDiv = e.target.closest('.comment');
        const reviewId = commentDiv.querySelector('.remove').getAttribute('data-review-id');
        const replyInput = commentDiv.querySelector('.reply-input');
        const replyText = replyInput.value.trim();

        if (replyText === "") {
            alert("Reply cannot be empty.");
            return;
        }

        // Update reply via AJAX
        fetch('admin_reply.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `review_id=${reviewId}&reply=${encodeURIComponent(replyText)}`
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                const replyContainer = commentDiv.querySelector('.admin-reply');
                replyContainer.innerHTML = `<strong class="text-primary">Admin Reply:</strong> ${replyText}`;
                replyInput.value = ""; // Clear the input box
                commentDiv.querySelector('.reply-box').style.display = "none"; // Hide reply box
                commentDiv.querySelector('.edit-reply').style.display = "inline"; // Show Edit button
            } else {
                alert("Failed to save reply.");
            }
        });
    }

    // Edit existing reply
    if (e.target.classList.contains('edit-reply')) {
        const commentDiv = e.target.closest('.comment');
        const currentReply = commentDiv.querySelector('.admin-reply-text').textContent.trim();
        const replyInput = commentDiv.querySelector('.reply-input');
        const replyBox = commentDiv.querySelector('.reply-box');
        
        replyInput.value = currentReply; // Populate the input with the existing reply
        replyBox.style.display = 'block'; // Show the reply input box
        e.target.style.display = 'none'; // Hide the "Edit" button
    }

    // Remove review
    if (e.target.classList.contains('remove')) {
        const reviewDiv = e.target.closest('.comment');
        const reviewId = e.target.getAttribute('data-review-id');

        if (confirm("Are you sure you want to delete this review?")) {
            fetch('delete_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `review_id=${reviewId}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    reviewDiv.remove(); // Remove the review from the DOM
                } else {
                    alert("Failed to delete review.");
                }
            });
        }
    }
});
</script>

</body>
</html>