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
                <a href="admin_dashboard.html" class="">
                    <i class="bi bi-grid-fill"></i>
                    <h3>Dashboard</h3>
                </a>
                <a href="admin_users.php" class="">
                    <i class="bi bi-people-fill"></i>
                    <h3>Users</h3>
                </a>
                <a href="admin_inventory_v2.html" class="">
                    <i class="bi bi-box2-heart-fill"></i>
                    <h3>Products</h3>
                </a>
                <a href="admin_orders.html" class="">
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
                            <button class="carousel-btn prev">&lt;</button>
                            <button class="carousel-btn next">&gt;</button>
                        </div>
                    </div>
                    
                    <button id="addImagesBtn">Add Images</button>
                    <input type="file" id="imageInput" accept="image/*" multiple style="display: none;">                    
                    
                </div>

                <div class="product-details">
                    <div class="form-group">
                        <label for="product_title"><strong>Product Title:</strong></label>
                        <input type="text" id="product_title" name="product_title" class="styled-input">
                    </div>
                
                    <div class="form-group">
                        <label for="description"><strong>Product Description:</strong></label>
                        <textarea id="description" name="description" class="styled-input"></textarea>
                    </div>
                
                    <div class="form-group">
                        <label for="price"><strong>Product Price:</strong> ₱</label>
                        <input type="number" id="price" step="0.01" name="price" class="styled-input">
                    </div>
                
                    <div class="form-group">
                        <label for="quantity"><strong>Product Quantity:</strong></label>
                        <input type="number" id="quantity" name="quantity" class="styled-input">
                    </div>
                
                    <div class="form-group">
                        <label for="category"><strong>Category:</strong></label>
                        <div class="custom-select-wrapper">
                            <select id="category" name="category_id" class="custom-select" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="Category_1">Category 1</option>
                                <option value="Category_2">Category 2</option>
                                <option value="Category_3">Category 3</option>
                                <option value="Category_4">Category 4</option>
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
            

            <div class="actions">
                <span class="action-link update">Update</span>
                <a href="admin_inventory_v2.html"><span class="action-link cancel">Cancel</span></a>
            </div>

            <div class="customer-reviews">
                <h2>
                    Customer Reviews and Comments
                </h2>
                <div class="star">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                    <i class="bi bi-star"></i>
                    <small class="text-muted">(12 reviews)</small>
                </div>

                <div class="comments">
                    <div id="reviewsContainer">
                        <!-- Example review -->
                        <div class="comment">
                            <div class="name-and-date">
                                <strong>Jane Doe</strong>
                                <small class="text-muted">12/23/25</small>
                            </div>
                                <div class="star text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p>Love the texture and quality!</p>
                                <div class="comment-action">
                                    <span class="action-link reply">reply</span> | 
                                    <span class="action-link remove">remove</span>
                                </div>
                                <div class="reply-box" style="display: none; margin-top: 0.5rem;">
                                    <input type="text" class="reply-input" placeholder="Type your reply..." />
                                    <button class="submit-reply">Post</button>
                                </div>
                                <div class="admin-reply" style="margin-top: 0.5rem;"></div>
                        </div>

                        <div class="comment">
                            <div class="name-and-date">
                                <strong>Chris P. Bacon</strong>
                                <small class="text-muted">12/23/25</small>
                            </div>
                                <div class="star text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p>Love the texture and quality!</p>
                                <div class="comment-action">
                                    <span class="action-link reply">reply</span> | 
                                    <span class="action-link remove">remove</span>
                                </div>
                        </div>

                        <div class="comment">
                            <div class="name-and-date">
                                <strong>Deez nuts</strong>
                                <small class="text-muted">09/05/25</small>
                            </div>
                                <div class="star text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p>Love the texture and quality!</p>
                                <div class="comment-action">
                                    <span class="action-link reply">reply</span> | 
                                    <span class="action-link remove">remove</span>
                                </div>
                        </div>

                        <div class="comment">
                            <div class="name-and-date">
                                <strong>Hephep Hooray</strong>
                                <small class="text-muted">01/31/25</small>
                            </div>
                                <div class="star text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p>Love the texture and quality!</p>
                                <div class="comment-action">
                                    <span class="action-link reply">reply</span> | 
                                    <span class="action-link remove">remove</span>
                                </div>
                        </div>

                        <div class="comment">
                            <div class="name-and-date">
                                <strong>Ina D. Pota</strong>
                                <small class="text-muted">11/23/24</small>
                            </div>
                                <div class="star text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p>Love the texture and quality!</p>
                                <div class="comment-action">
                                    <span class="action-link reply">reply</span> | 
                                    <span class="action-link remove">remove</span>
                                </div>
                        </div>
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
            const categorySelect = document.getElementById('category');

            if (input.value.trim()) {
                const newOption = document.createElement('option');
                newOption.value = input.value.trim();
                newOption.textContent = input.value.trim();
                categorySelect.appendChild(newOption);
                input.value = '';
                addCategoryModal.style.display = 'none';
            } else {
                alert('Please enter a category name.');
            }
        });

        // Delete Category Logic
        document.getElementById('confirmDeleteCategory').addEventListener('click', () => {
            const deleteSelect = document.getElementById('deleteCategorySelect');
            const categorySelect = document.getElementById('category');

            const optionToDelete = Array.from(categorySelect.options).find(opt => opt.value === deleteSelect.value);
            if (optionToDelete) {
                categorySelect.removeChild(optionToDelete);
                deleteCategoryModal.style.display = 'none';
            }
        });
        
        
document.querySelectorAll('.reply').forEach(button => {
    button.addEventListener('click', () => {
        const commentBox = button.closest('.comment');
        const replyBox = commentBox.querySelector('.reply-box');
        replyBox.style.display = replyBox.style.display === 'none' ? 'block' : 'none';
    });
});

document.querySelectorAll('.submit-reply').forEach(button => {
    button.addEventListener('click', () => {
        const commentBox = button.closest('.comment');
        const input = commentBox.querySelector('.reply-input');
        const reply = input.value.trim();

        if (reply !== '') {
            const replyContainer = commentBox.querySelector('.admin-reply');
            const replyElement = document.createElement('p');
            replyElement.innerHTML = `<strong>Purrfectly Stitched:</strong> ${reply}`;
            replyContainer.appendChild(replyElement);

            // Reset input
            input.value = '';
            commentBox.querySelector('.reply-box').style.display = 'none';
        }
    });
});

    </script>    
    

</body>
</html>