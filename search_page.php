<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_wishlist'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);

   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_wishlist_numbers->rowCount() > 0){
      $message[] = 'already added to wishlist!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'added to wishlist!';
   }

}

if(isset($_POST['add_to_cart'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'added to cart!';
   }

}



// Fetch categories and conditions
$select_categories = $conn->prepare("SELECT * FROM categories");
$select_categories->execute();
$categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);

$select_conditions = $conn->prepare("SELECT * FROM conditions");
$select_conditions->execute();
$conditions = $select_conditions->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style>




body {
   background-image: url('images/2bg.png') !important;
   background-size: cover;
   background-position: center;
   background-attachment: fixed;
   background-repeat: no-repeat;
   font-family: 'Rubik', sans-serif;
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   color: var(--black);
}
        .active-tab {
    background-color: #007bff;
    color: white;
      }

            .active {
    background-color: #28a745;
    color: white;
        }
        
        
        .filter-buttons {
            display: flex;
            gap: 10px; /* Space between buttons */
            margin-top: 10px; /* Space below search bar */
        }

        .filter-buttons button {
            padding: 8px 16px;
            border: none;
            background-color: #eee; /* Light gray */
            cursor: pointer;
            border-radius: 5px;
        }

        .filter-buttons button.active {
            background-color: #ddd; /* Slightly darker gray for active button */
        }

        .subcategory-buttons {
            display: none; /* Initially hidden */
            flex-wrap: wrap; /* Allow wrapping to new lines */
            gap: 10px;
            margin-top: 10px;
        }
        .subcategory-buttons button{
            padding: 8px 16px;
            border: none;
            background-color: #eee; /* Light gray */
            cursor: pointer;
            border-radius: 5px;
        }
        .subcategory-buttons button.active{
            background-color: #ddd; /* Slightly darker gray for active button */
        }
    </style>
</head>


<body>
   
<?php include 'header.php'; ?>

<section class="search-form">
   <form action="" method="POST">
      <input type="text" class="box" name="search_box" id="search_box" placeholder="search products..." autocomplete="off">
      <input type="submit" name="search_btn" value="search" class="btn">
   </form>
   <!-- Suggestions will be shown here -->
   <div id="search_suggestions"></div>

   <div class="filter-buttons">
            <button id="category-button">Categories</button>
            <button id="condition-button">Conditions</button>
        </div>

        <div class="subcategory-buttons" id="category-subcategories">
            <?php if ($categories) : ?>
                <?php foreach ($categories as $category) : ?>
                    <button data-category="<?= $category['id'] ?>"><?= $category['category_name'] ?></button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="subcategory-buttons" id="condition-subcategories">
            <?php if ($conditions) : ?>
                <?php foreach ($conditions as $condition) : ?>
                    <button data-condition="<?= $condition['id'] ?>"><?= $condition['condition_name'] ?></button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="subcategory-buttons" id="subcategories-container" style="display:none;"></div>
     </section>



<section class="products" style="padding-top: 0; min-height:100vh;">
   <div class="box-container">
   <?php
     if(isset($_GET['query'])) {
      $search_box = $_GET['query'];
      $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? OR category LIKE ?");
      $select_products->execute(['%' . $search_box . '%', '%' . $search_box . '%']);
   }
   
      else {
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
      }

      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
      <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
      <input type="number" min="1" value="1" name="p_qty" class="qty">
      <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no result found!</p>';
      }
   ?>
   </div>
</section>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script>
// AJAX to fetch suggestions as the user types in the search box
document.getElementById('search_box').addEventListener('input', function() {
    let query = this.value.trim();
    if (query.length > 0) {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'search_suggestions.php?query=' + encodeURIComponent(query), true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById('search_suggestions').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        document.getElementById('search_suggestions').innerHTML = '';
    }
});

// Handle suggestion click
function selectSuggestion(value) {
    document.getElementById('search_box').value = value;
    document.getElementById('search_suggestions').innerHTML = '';
    window.location.href = 'search_page.php?query=' + encodeURIComponent(value);
}

// Category & Condition Buttons
const categoryButton = document.getElementById('category-button');
const conditionButton = document.getElementById('condition-button');
const categorySubcategories = document.getElementById('category-subcategories');
const conditionSubcategories = document.getElementById('condition-subcategories');
const subcategoriesContainer = document.getElementById('subcategories-container');
const productContainer = document.querySelector('.box-container');

let activeCategory = null;
let activeSubcategory = null;
let activeCondition = null;

// Toggle Category Panel
categoryButton.addEventListener('click', () => {
    togglePanel(categorySubcategories);
    conditionSubcategories.style.display = 'none';
    subcategoriesContainer.style.display = 'none';

    // Reset condition selection when switching to categories
    resetConditionSelection();

    toggleActiveState(categoryButton, conditionButton);
});

// Toggle Condition Panel
conditionButton.addEventListener('click', () => {
    togglePanel(conditionSubcategories);
    categorySubcategories.style.display = 'none';
    subcategoriesContainer.style.display = 'none';

    // Reset category and subcategory selection when switching to conditions
    resetCategorySelection();

    toggleActiveState(conditionButton, categoryButton);
});

// Handle Category Click
categorySubcategories.querySelectorAll('button').forEach(button => {
    button.addEventListener('click', () => {
        const categoryId = button.dataset.category;
        activeCategory = categoryId;
        activeSubcategory = null; // Reset subcategory
        setActiveButton(button, categorySubcategories);

        fetch(`fetch_subcategories.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(subcategories => {
                subcategoriesContainer.innerHTML = '';
                if (subcategories.length > 0) {
                    subcategories.forEach(subcategory => {
                        const subButton = document.createElement('button');
                        subButton.dataset.subcategory = subcategory.id;
                        subButton.textContent = subcategory.subcategory_name;
                        subButton.addEventListener('click', () => {
                            activeSubcategory = subcategory.id;
                            setActiveButton(subButton, subcategoriesContainer);
                            filterProducts();
                        });
                        subcategoriesContainer.appendChild(subButton);
                    });
                    subcategoriesContainer.style.display = 'flex';
                } else {
                    subcategoriesContainer.style.display = 'none';
                }
                filterProducts();
            });
    });
});

// Handle Condition Click
conditionSubcategories.querySelectorAll('button').forEach(button => {
    button.addEventListener('click', () => {
        activeCondition = button.dataset.condition;
        setActiveButton(button, conditionSubcategories);
        filterProducts();
    });
});

// Function to Highlight Active Button
function setActiveButton(clickedButton, buttonContainer) {
    buttonContainer.querySelectorAll('button').forEach(button => button.classList.remove('active'));
    clickedButton.classList.add('active');
}

// Function to Toggle Category & Condition Panels
function togglePanel(panel) {
    panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
}

// Function to Toggle Active Tab Colors
function toggleActiveState(active, inactive) {
    active.classList.add('active-tab');
    inactive.classList.remove('active-tab');
}

// Function to Reset Condition Selection
function resetConditionSelection() {
    activeCondition = null;
    conditionSubcategories.querySelectorAll('button').forEach(button => button.classList.remove('active'));
}

// Function to Reset Category & Subcategory Selection
function resetCategorySelection() {
    activeCategory = null;
    activeSubcategory = null;
    categorySubcategories.querySelectorAll('button').forEach(button => button.classList.remove('active'));
    subcategoriesContainer.innerHTML = ''; // Clear subcategories
    subcategoriesContainer.style.display = 'none';
}

// Function to Fetch & Display Filtered Products
function filterProducts() {
    let url = 'fetch_products.php?';
    if (activeCategory) url += `category_id=${activeCategory}`;
    if (activeSubcategory) url += `&subcategory_id=${activeSubcategory}`;
    if (activeCondition) url += `&condition_id=${activeCondition}`;

    fetch(url)
        .then(response => response.text())
        .then(data => {
            productContainer.innerHTML = data;
        });
}
</script>




</body>
</html>