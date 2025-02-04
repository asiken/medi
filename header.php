<?php
// Start the session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if a message is set and display it
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

// Check if user is logged in (ensure user_id is set in the session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>


<header class="header">

   <div class="flex">

      <a href="home.php" class="logo">Mansion<span>Chemist</span></a>

      <nav class="navbar">
         <a href="home.php">home</a>
         <a href="shop.php">shop</a>
         <a href="orders.php">orders</a>
         <a href="contact.php">contact</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <a href="search_page.php" class="fas fa-search"></a>
         <?php
            if ($user_id) {
                $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $count_cart_items->execute([$user_id]);
                $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
                $count_wishlist_items->execute([$user_id]);
                echo '<a href="wishlist.php"><i class="fas fa-heart"></i><span>(' . $count_wishlist_items->rowCount() . ')</span></a>';
                echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(' . $count_cart_items->rowCount() . ')</span></a>';
            } else {
                // For guests (when not logged in)
                echo '<a href="wishlist.php"><i class="fas fa-heart"></i><span>(0)</span></a>';
                echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(0)</span></a>';
            }
         ?>
      </div>

      <div class="profile">
         <?php
            if ($user_id) {
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                echo '<img src="uploaded_img/' . $fetch_profile['image'] . '" alt="">';
                echo '<p>' . $fetch_profile['name'] . '</p>';
                echo '<a href="user_profile_update.php" class="btn">update profile</a>';
                echo '<a href="logout.php" class="delete-btn">logout</a>';
            } else {
                // For guests (when not logged in)
                echo '<div class="flex-btn">';
                echo '<a href="login.php" class="option-btn">login</a>';
                echo '<a href="register.php" class="option-btn">register</a>';
                echo '</div>';
            }
         ?>
      </div>

   </div>

</header>
