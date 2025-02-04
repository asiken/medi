<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
}

if(isset($_POST['update_product'])){

   $pid = $_POST['pid'];
   $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
   $category_id = $_POST['category_id'];
   $subcategory_id = $_POST['subcategory_id'];
   $condition_id = $_POST['condition_id'];
   $details = filter_var($_POST['details'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $old_image = $_POST['old_image'];

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category_id = ?, subcategory_id = ?, condition_id = ?, details = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $category_id, $subcategory_id, $condition_id, $details, $price, $pid]);

   $message[] = 'Product updated successfully!';

   if(!empty($_FILES['image']['name'])){
      $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES, 'UTF-8');
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_size = $_FILES['image']['size'];
      $image_folder = 'uploaded_img/'.$image;
      $image_ext = pathinfo($image, PATHINFO_EXTENSION);
      $allowed_ext = ['jpg', 'jpeg', 'png'];

      if($image_size > 2000000){
         $message[] = 'Image size is too large!';
      } elseif (!in_array(strtolower($image_ext), $allowed_ext)) {
         $message[] = 'Invalid image format! Only JPG, JPEG, and PNG allowed.';
      } else {
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);

         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            if(file_exists('uploaded_img/'.$old_image)){
               unlink('uploaded_img/'.$old_image);
            }
            $message[] = 'Image updated successfully!';
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>

<style>
 body
{
            background-image: url('images/5bg.png') !important;
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
  }</style>


<body>
   
<?php include 'admin_header.php'; ?>

<section class="update-product">
   <h1 class="title">Update Product</h1>   

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$update_id]);

         if($select_products->rowCount() > 0){
            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">

      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="Product Image">

      <input type="text" name="name" placeholder="Enter product name" required class="box" value="<?= $fetch_products['name']; ?>">
      <input type="number" name="price" min="0" placeholder="Enter product price" required class="box" value="<?= $fetch_products['price']; ?>">

      <!-- Category Dropdown -->
      <select name="category_id" class="box" required>
         <option selected disabled>Select Category</option>
         <?php
            $select_categories = $conn->prepare("SELECT * FROM `categories`");
            $select_categories->execute();
            while($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
               $selected = ($fetch_products['category_id'] == $fetch_category['id']) ? 'selected' : '';
               echo "<option value='{$fetch_category['id']}' $selected>{$fetch_category['category_name']}</option>";
            }
         ?>
      </select>

      <!-- Subcategory Dropdown -->
      <select name="subcategory_id" class="box" required>
         <option selected disabled>Select Subcategory</option>
         <?php
            $select_subcategories = $conn->prepare("SELECT * FROM `subcategories`");
            $select_subcategories->execute();
            while($fetch_subcategory = $select_subcategories->fetch(PDO::FETCH_ASSOC)) {
               $selected = ($fetch_products['subcategory_id'] == $fetch_subcategory['id']) ? 'selected' : '';
               echo "<option value='{$fetch_subcategory['id']}' $selected>{$fetch_subcategory['subcategory_name']}</option>";
            }
         ?>
      </select>

      <!-- Condition Dropdown -->
      <select name="condition_id" class="box" required>
         <option selected disabled>Select Condition</option>
         <?php
            $select_conditions = $conn->prepare("SELECT * FROM `conditions`");
            $select_conditions->execute();
            while($fetch_condition = $select_conditions->fetch(PDO::FETCH_ASSOC)) {
               $selected = ($fetch_products['condition_id'] == $fetch_condition['id']) ? 'selected' : '';
               echo "<option value='{$fetch_condition['id']}' $selected>{$fetch_condition['condition_name']}</option>";
            }
         ?>
      </select>

      <textarea name="details" required placeholder="Enter product details" class="box" cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      
      <div class="flex-btn">
         <input type="submit" class="btn" value="Update Product" name="update_product">
         <a href="admin_products.php" class="option-btn">Go Back</a>
      </div>
   </form>
   <?php
         } else {
            echo '<p class="empty">No products found!</p>';
         }
      } else {
         echo '<p class="empty">Invalid request!</p>';
      }
   ?>
</section>

<script src="js/script.js"></script>

</body>
</html>
