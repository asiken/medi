<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
   $category_id = $_POST['category_id'];
   $subcategory_id = $_POST['subcategory_id'];
   $condition_id = $_POST['condition_id'];
   $details = filter_var($_POST['details'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $image = $_FILES['image']['name'];
   $image = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
   if (!in_array($_FILES['image']['type'], $allowed_types)) {
       $message[] = 'Only JPG, JPEG, and PNG formats are allowed!';
   } else {
       $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
       $select_products->execute([$name]);

       if($select_products->rowCount() > 0){
           $message[] = 'Product name already exists!';
       } else {
           $insert_products = $conn->prepare("INSERT INTO `products`(name, category_id, subcategory_id, condition_id, details, price, image) VALUES(?,?,?,?,?,?,?)");
           $insert_products->execute([$name, $category_id, $subcategory_id, $condition_id, $details, $price, $image]);

           if($image_size > 2000000){
               $message[] = 'Image size is too large!';
           } else {
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'New product added!';
           }
       }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   
   $check_product = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $check_product->execute([$delete_id]);
   if($check_product->rowCount() > 0){
       $fetch_delete_image = $check_product->fetch(PDO::FETCH_ASSOC);
       unlink('uploaded_img/'.$fetch_delete_image['image']);
       
       $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
       $delete_products->execute([$delete_id]);
       $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
       $delete_wishlist->execute([$delete_id]);
       $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
       $delete_cart->execute([$delete_id]);
   }
   header('location:admin_products.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>

<style>
   
/* General styles */
body {
            background-image: url('images/3bg.png') !important;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Rubik', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: var(--black);
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
         }
        

a {
   text-decoration: none;
   color: #0077cc;
}

h1.title {
   font-size: 2rem;
   color: #1d3557;
   text-align: center;
   margin-bottom: 20px;
}

/* Header Style */
header {
   background-color: #1d3557;
   padding: 20px;
   text-align: center;
   color: white;
}



/* Styling for the select element */
select {
   background-color: #ffffff; /* Light background for the dropdown */
   color: #333333; /* Dark text color for the dropdown options */
   border: 1px solid #ccc; /* Light border color */
   border-radius: 8px;
   font-size: 1rem;
   padding: 10px;
   width: 100%;
   box-sizing: border-box;
   appearance: none; /* Remove default browser appearance */
   -webkit-appearance: none; /* Remove default browser appearance in Webkit browsers */
   -moz-appearance: none; /* Remove default browser appearance in Firefox */
}

/* Add a custom dropdown arrow */
select::after {
   content: ' ▼'; /* Adds a custom arrow */
   color: #333; /* Make the arrow dark */
   font-size: 1.2rem;
   padding-left: 10px;
}

/* Styling for the options inside the dropdown */
select option {
   background-color: #ffffff; /* White background for options */
   color: #333333; /* Dark text color for options */
}

/* Ensure the options inside select are clearly visible */
select:focus {
   border-color: #1d3557; /* Dark border when focused */
   outline: none; /* Remove default focus outline */
}

/* Styling for the container for dropdown arrows */
select::-ms-expand {
   display: none; /* Remove dropdown arrow in IE */
}


select::-webkit-appearance {
   none; /* Removes default arrow in Chrome/Safari */
}

select::-moz-appearance {
   none; /* Removes default arrow in Firefox */
}

/* Add Product Form */
.add-products {
   padding: 40px;
   background-color: #ffffff;
   border-radius: 10px;
   box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
   margin: 30px auto;
   max-width: 1200px;
}

.add-products .inputBox {
   margin-bottom: 20px;
}

.add-products .inputBox input,
.add-products .inputBox select,
.add-products textarea {
   width: 100%;
   padding: 10px;
   margin-bottom: 20px;
   border: 1px solid #ccc;
   border-radius: 8px;
   font-size: 1rem;
   transition: all 0.3s ease;
}

.add-products .inputBox input:focus,
.add-products .inputBox select:focus,
.add-products textarea:focus {
   border-color: #1d3557;
}

.add-products .btn {
   background-color: #28a745;
   color: white;
   padding: 15px 30px;
   border-radius: 8px;
   cursor: pointer;
   transition: all 0.3s ease;
}

.add-products .btn:hover {
   background-color: #218838;
}

/* Show Products Section */
.show-products {
   padding: 40px;
   background-color: #ffffff;
   border-radius: 10px;
   box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
   margin: 30px auto;
   max-width: 1200px;
}

.show-products .box-container {
   display: flex;
   flex-wrap: wrap;
   gap: 20px;
   justify-content: space-between;
}

.show-products .box {
   background-color: #fff;
   box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
   border-radius: 10px;
   width: 23%;
   text-align: center;
   padding: 20px;
   transition: all 0.3s ease;
   position: relative;
}

.show-products .box:hover {
   box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
   transform: translateY(-5px);
}

.show-products .box .price {
   font-size: 1.2rem;
   color: #28a745;
}

.show-products .box img {
   max-width: 100%;
   border-radius: 10px;
   margin: 15px 0;
}

.show-products .box .name {
   font-size: 1.1rem;
   font-weight: bold;
   color: #1d3557;
}

.show-products .box .details {
   color: #555;
   font-size: 0.9rem;
}

.show-products .box .flex-btn {
   display: flex;
   justify-content: space-between;
   margin-top: 10px;
}

.show-products .box .flex-btn a {
   background-color: #0077cc;
   color: white;
   padding: 8px 16px;
   border-radius: 5px;
   text-align: center;
   width: 45%;
   transition: all 0.3s ease;
}

.show-products .box .flex-btn a:hover {
   background-color: #005fa3;
}

/* Delete Button */
.delete-btn {
   background-color: #e53e3e;
   color: white;
   padding: 8px 16px;
   border-radius: 5px;
   width: 45%;
   text-align: center;
   transition: all 0.3s ease;
}

.delete-btn:hover {
   background-color: #c53030;
}

.empty {
   text-align: center;
   color: #e53e3e;
   font-size: 1.2rem;
}

/* Responsive Styles */
@media (max-width: 768px) {
   .add-products, .show-products {
      padding: 20px;
      max-width: 100%;
   }

   .show-products .box-container {
      flex-direction: column;
      gap: 10px;
   }

   .show-products .box {
      width: 100%;
   }
}


</style>



<body>
   
  <?php include 'admin_header.php'; ?>

<section class="add-products">
   <h1 class="title">Add New Product</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <input type="text" name="name" class="box" required placeholder="Enter product name">
            <select name="category_id" class="box" required>
               <option value="" selected disabled>Select Category</option>
               <?php
                  $select_categories = $conn->prepare("SELECT * FROM `categories`");
                  $select_categories->execute();
                  while($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='{$fetch_category['id']}'>{$fetch_category['category_name']}</option>";
                  }
               ?>
            </select>
         </div>
         <div class="inputBox">
            <select name="subcategory_id" class="box" required>
               <option value="" selected disabled>Select Subcategory</option>
               <?php
                  $select_subcategories = $conn->prepare("SELECT * FROM `subcategories`");
                  $select_subcategories->execute();
                  while($fetch_subcategory = $select_subcategories->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='{$fetch_subcategory['id']}'>{$fetch_subcategory['subcategory_name']}</option>";
                  }
               ?>
            </select>
         </div>
         <div class="inputBox">
            <select name="condition_id" class="box" required>
               <option value="" selected disabled>Select Condition</option>
               <?php
                  $select_conditions = $conn->prepare("SELECT * FROM `conditions`");
                  $select_conditions->execute();
                  while($fetch_condition = $select_conditions->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='{$fetch_condition['id']}'>{$fetch_condition['condition_name']}</option>";
                  }
               ?>
            </select>
         </div>
         <div class="inputBox">
            <input type="number" min="0" name="price" class="box" required placeholder="Enter product price">
            <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="Enter product details" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="Add Product" name="add_product">
   </form>
</section>




<section class="add-products">
    <h2>Add/Manage Product Groups</h2>  </br>
    <form action="process_product_groups.php" method="POST">  </br>
        <label for="product_id">Select Product:</label>
        <select name="product_id" id="product_id" required>
            <?php
            $select_products = $conn->prepare("SELECT id, name FROM products");
            $select_products->execute();
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$fetch_product['id']}'>{$fetch_product['name']}</option>";
            }
            ?>
        </select> </br> </br>

        <label>Select Group(s):</label>  </br>
        <input type="checkbox" name="groups[]" value="new_arrivals"> New Arrivals  </br>
        <input type="checkbox" name="groups[]" value="health_essentials"> Health Essentials  </br>
        <input type="checkbox" name="groups[]" value="best_sellers"> Best Sellers  </br>

      
      </br>

        <input type="submit" value="Update Product Groups" name="update_groups">
    </form>
</section>









<section class="show-products">
   <h1 class="title">Products Added</h1>
   <div class="box-container">
      <?php
        $show_products = $conn->prepare("SELECT p.*, 
        c.id as category_id, c.category_name, 
        s.id as subcategory_id, s.subcategory_name, 
        d.id as condition_id, d.condition_name 
 FROM `products` p 
 JOIN `categories` c ON p.category_id = c.id 
 JOIN `subcategories` s ON p.subcategory_id = s.id 
 JOIN `conditions` d ON p.condition_id = d.id");
$show_products->execute();

         if($show_products->rowCount() > 0){
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
      ?>
      <div class="box">
    <div class="price">$<?= $fetch_products['price']; ?>/-</div>
    <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
    <div class="name"><?= $fetch_products['name']; ?></div>
    <div class="cat"><?= $fetch_products['category_name']; ?> (ID: <?= $fetch_products['category_id']; ?>)</div>
    <div class="subcat"><?= $fetch_products['subcategory_name']; ?> (ID: <?= $fetch_products['subcategory_id']; ?>)</div>
    <div class="condition"><?= $fetch_products['condition_name']; ?> (ID: <?= $fetch_products['condition_id']; ?>)</div>
    <div class="details"><?= $fetch_products['details']; ?></div>
    <div class="flex-btn">
        <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
        <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
    </div>
</div>
      <?php
         }
      }
      ?>
   </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
   let userBtn = document.querySelector("#user-btn");
   let menuBtn = document.querySelector("#menu-btn");
   let profile = document.querySelector(".profile");
   let navbar = document.querySelector(".navbar");

   // Toggle profile dropdown
   if (userBtn && profile) {
      userBtn.addEventListener("click", function () {
         profile.classList.toggle("active");
         navbar.classList.remove("active"); // Close menu if open
      });
   }

   // Toggle navbar
   if (menuBtn && navbar) {
      menuBtn.addEventListener("click", function () {
         navbar.classList.toggle("active");
         profile.classList.remove("active"); // Close profile if open
      });
   }

   // Close dropdowns when clicking outside
   document.addEventListener("click", function (e) {
      if (!userBtn.contains(e.target) && !profile.contains(e.target)) {
         profile.classList.remove("active");
      }
      if (!menuBtn.contains(e.target) && !navbar.contains(e.target)) {
         navbar.classList.remove("active");
      }
   });
});
</script>






</body>
</html>

