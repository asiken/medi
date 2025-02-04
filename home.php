<?php
@include 'config.php';
session_start();

// Check if the user is logged in only when they add to cart or wishlist
if (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) {
    $user_id = $_SESSION['user_id'] ?? null;  // Get user ID or null if not logged in

    // If the user is not logged in, prompt them to log in
    if (!isset($user_id)) {
        header('location: login.php');  // Redirect to login page
        exit;
    }

    // Process the wishlist addition
    if (isset($_POST['add_to_wishlist'])) {
        $pid = $_POST['pid'];
        $pid = filter_var($pid, FILTER_SANITIZE_STRING);
        $p_name = $_POST['p_name'];
        $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
        $p_price = $_POST['p_price'];
        $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
        $p_image = $_POST['p_image'];
        $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);

        // Check if the product is already in wishlist or cart
        $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $check_wishlist_numbers->execute([$p_name, $user_id]);

        $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $check_cart_numbers->execute([$p_name, $user_id]);

        if ($check_wishlist_numbers->rowCount() > 0) {
            $message[] = 'Already added to wishlist!';
        } elseif ($check_cart_numbers->rowCount() > 0) {
            $message[] = 'Already added to cart!';
        } else {
            $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
            $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
            $message[] = 'Added to wishlist!';
        }
    }

    // Process the cart addition
    if (isset($_POST['add_to_cart'])) {
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

        // Check if the product is already in the cart
        $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $check_cart_numbers->execute([$p_name, $user_id]);

        if ($check_cart_numbers->rowCount() > 0) {
            $message[] = 'Already added to cart!';
        } else {
            // Check if the product is in the wishlist and remove it from there
            $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
            $check_wishlist_numbers->execute([$p_name, $user_id]);

            if ($check_wishlist_numbers->rowCount() > 0) {
                $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
                $delete_wishlist->execute([$p_name, $user_id]);
            }

            // Add to cart
            $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
            $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
            $message[] = 'Added to cart!';
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
   <title>Home Page</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/components.css">
   <link rel="stylesheet" href="css/animate.css">

   <style>
       body {
        background-color: #fdfdfd;
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

       .products {
    text-align: center;
    margin: 20px 0;
}

.products .title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.carousel-container {
    display: flex;
    align-items: center;
    position: relative;
}

.products .box-container {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    padding: 10px;
    width: 90%;
    margin: 0 auto;
}

.products .box {
    min-width: 200px;
    max-width: 220px;
    margin: 0 10px;
    border-radius: 10px;
    flex-shrink: 0;
    scroll-snap-align: center;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 15px;
    transition: transform 0.3s ease-in-out;
}

.products .box:hover {
    transform: scale(1.05);
}

.products .box img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.products .box .name {
    font-size: 14px;
    font-weight: bold;
    margin: 10px 0;
}

.products .box .price {
    font-size: 16px;
    color: #d32f2f;
    font-weight: bold;
}

.products .box .btn {
    font-size: 14px;
    padding: 8px 12px;
    background-color: #d32f2f;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

.products .box .btn:hover {
    background-color: #b71c1c;
}

.carousel-btn {
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    cursor: pointer;
    padding: 10px 15px;
    font-size: 18px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 100;
    border-radius: 50%;
}

.carousel-btn.prev {
    left: 0;
}

.carousel-btn.next {
    right: 0;
}

.carousel-indicators {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.carousel-indicators button {
    width: 10px;
    height: 10px;
    margin: 0 5px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.3);
    border: none;
    cursor: pointer;
}

.carousel-indicators button.active {
    background-color: rgba(0, 0, 0, 0.7);
}

@media (max-width: 768px) {
    .products .box {
        min-width: 180px;
    }
}

   </style>
</head>
<body>
   
   <?php include 'header.php'; ?>

   <section id="slider">
       <div class="container">
           <div class="row">
               <div class="col-sm-12">
                   <div id="slider-carousel" class="carousel slide" data-bs-ride="carousel">
                       <ol class="carousel-indicators">
                           <li data-bs-target="#slider-carousel" data-bs-slide-to="0" class="active"></li>
                           <li data-bs-target="#slider-carousel" data-bs-slide-to="1"></li>
                           <li data-bs-target="#slider-carousel" data-bs-slide-to="2"></li>
                       </ol>
                       




                       <div class="carousel-inner">
                           <div class="carousel-item active">
                               <div class="col-sm-6">
                                   <h1><span>MANSION</span> CHEMIST</h1>
                                   <h2>Your Trusted Online Pharmacy</h2>
                                   <p>Order your medications and healthcare essentials with ease and have them delivered to your doorstep.</p>
                               </div>
                               <div class="col-sm-6">
                                   <img src="images/pharmacy1.jpg" class="img-responsive" alt="Pharmacy service" />
                               </div>
                           </div>
                           

                           
                           <div class="carousel-item">
                               <div class="col-sm-6">
                                   <h1><span>MANSION</span> CHEMIST</h1>
                                   <h2>Quality Healthcare at Your Fingertips</h2>
                                   <p>Browse a wide range of prescription and over-the-counter medications from the comfort of your home.</p>
                               </div>
                               <div class="col-sm-6">
                                   <img src="images/pharmacy2.jpg" class="img-responsive" alt="Medicine delivery" />
                               </div>
                           </div>

                           <div class="carousel-item">
                               <div class="col-sm-6">
                                   <h1><span>MANSION</span> CHEMIST</h1>
                                   <h2>Fast & Reliable Delivery</h2>
                                   <p>Get your essential medications delivered quickly and safely, ensuring your health is always a priority.</p>
                               </div>
                               <div class="col-sm-6">
                                   <img src="images/pharmacy3.jpg" class="img-responsive" alt="Medicine delivery service" />
                               </div>
                           </div>
                       </div>

                       <a href="#slider-carousel" class="carousel-control-prev" data-bs-slide="prev">
                           <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                           <span class="visually-hidden">Previous</span>
                       </a>
                       <a href="#slider-carousel" class="carousel-control-next" data-bs-slide="next">
                           <span class="carousel-control-next-icon" aria-hidden="true"></span>
                           <span class="visually-hidden">Next</span>
                       </a>
                   </div>
               </div>
           </div>
       </div>
   </section>

   <section class="products">
    <h1 class="title">NEW ARRIVALS</h1>
    <div class="carousel-container">
        <button class="carousel-btn prev" id="prevNewArrivals">&lt;</button>
        <div class="box-container" id="newArrivalsCarousel">
            <?php
            $select_new_arrivals = $conn->prepare("SELECT p.* FROM products p JOIN product_groups pg ON p.id = pg.product_id WHERE pg.group_name = 'new_arrivals' LIMIT 10");
            $select_new_arrivals->execute();
            if ($select_new_arrivals->rowCount() > 0) {
                while ($fetch_product = $select_new_arrivals->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" class="box" method="POST">
                <img src="uploaded_img/<?= $fetch_product['image']; ?>" alt="">
                <div class="name"><?= $fetch_product['name']; ?></div>
                <div class="price">Ksh <?= number_format($fetch_product['price']); ?></div>
                <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                <input type="hidden" name="p_name" value="<?= $fetch_product['name']; ?>">
                <input type="hidden" name="p_price" value="<?= $fetch_product['price']; ?>">
                <input type="hidden" name="p_image" value="<?= $fetch_product['image']; ?>">
                <input type="number" min="1" value="1" name="p_qty" class="qty">
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
            <?php
                }
            } else {
                echo '<p class="empty">No new arrivals found!</p>';
            }
            ?>
        </div>
        <button class="carousel-btn next" id="nextNewArrivals">&gt;</button>
    </div>
    <div class="carousel-indicators" id="newArrivalsIndicators"></div>
</section>


   <!-- Ad Banner -->
   <div class="ad-banner">
       <img src="images/ad-banner1.jpg" alt="Ad Banner">
   </div>

   <section class="products">
       <h1 class="title">BEST SELLERS</h1>
       <div class="box-container" id="bestSellersCarousel">
           <?php

   $select_best_sellers = $conn->prepare("SELECT p.* FROM products p JOIN product_groups pg ON p.id = pg.product_id WHERE pg.group_name = 'best_sellers' LIMIT 10");
           $select_best_sellers->execute();
           if ($select_best_sellers->rowCount() > 0) {
               while ($fetch_product = $select_best_sellers->fetch(PDO::FETCH_ASSOC)) {
           ?>
           <form action="" class="box" method="POST">
               <div class="price">Ksh <span><?= $fetch_product['price']; ?></span>/-</div>
               <a href="view_page.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
               <img src="uploaded_img/<?= $fetch_product['image']; ?>" alt="">
               <div class="name"><?= $fetch_product['name']; ?></div>
               <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
               <input type="hidden" name="p_name" value="<?= $fetch_product['name']; ?>">
               <input type="hidden" name="p_price" value="<?= $fetch_product['price']; ?>">
               <input type="hidden" name="p_image" value="<?= $fetch_product['image']; ?>">
               <input type="number" min="1" value="1" name="p_qty" class="qty">
               <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
           </form>
           <?php
               }
           } else {
               echo '<p class="empty">No best sellers found!</p>';
           }
           ?>
       </div>
       <div class="carousel-indicators" id="bestSellersIndicators"></div>
       <div class="carousel-nav">
           <button id="prevBestSellers">&lt; Prev</button>
           <button id="nextBestSellers">Next &gt;</button>
       </div>
   </section>

   <!-- Ad Banner -->
   <div class="ad-banner">
       <img src="images/ad-banner2.jpg" alt="Ad Banner">
   </div>

   <section class="products">
       <h1 class="title">HEALTH ESSENTIALS</h1>
       <div class="box-container" id="healthEssentialsCarousel">
           <?php
$select_health_essentials = $conn->prepare("SELECT p.* FROM products p JOIN product_groups pg ON p.id = pg.product_id WHERE pg.group_name = 'health_essentials' LIMIT 10");
$select_health_essentials->execute();
           if ($select_health_essentials->rowCount() > 0) {
               while ($fetch_product = $select_health_essentials->fetch(PDO::FETCH_ASSOC)) {
           ?>
           <form action="" class="box" method="POST">
               <div class="price">Ksh <span><?= $fetch_product['price']; ?></span>/-</div>
               <a href="view_page.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
               <img src="uploaded_img/<?= $fetch_product['image']; ?>" alt="">
               <div class="name"><?= $fetch_product['name']; ?></div>
               <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
               <input type="hidden" name="p_name" value="<?= $fetch_product['name']; ?>">
               <input type="hidden" name="p_price" value="<?= $fetch_product['price']; ?>">
               <input type="hidden" name="p_image" value="<?= $fetch_product['image']; ?>">
               <input type="number" min="1" value="1" name="p_qty" class="qty">
               <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
           </form>
           <?php
               }
           } else {
               echo '<p class="empty">No health essentials found!</p>';
           }
           ?>
       </div>
       <div class="carousel-indicators" id="healthEssentialsIndicators"></div>
       <div class="carousel-nav">
           <button id="prevHealthEssentials">&lt; Prev</button>
           <button id="nextHealthEssentials">Next &gt;</button>
       </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Custom JS -->
   <script src="js/script.js"></script>
   <script>
    // Generalized Carousel Functionality
    function setupCarousel(carouselId, indicatorsId, prevId, nextId) {
        const carousel = document.getElementById(carouselId);
        const indicators = document.getElementById(indicatorsId);
        const boxes = carousel.querySelectorAll('.box');
        let index = 0;

        function updateCarousel() {
            if (boxes.length > 0) {
                const boxWidth = boxes[0].getBoundingClientRect().width + 20;
                carousel.scrollLeft = index * boxWidth;

                const indicatorButtons = indicators.querySelectorAll('button');
                indicatorButtons.forEach((button, i) => {
                    button.classList.toggle('active', i === index);
                });
            }
        }

        if (boxes.length > 0) {
            boxes.forEach((_, i) => {
                const indicator = document.createElement('button');
                indicator.addEventListener('click', () => {
                    index = i;
                    updateCarousel();
                });
                indicators.appendChild(indicator);
            });
        }

        document.getElementById(prevId).addEventListener('click', () => {
            index = Math.max(0, index - 1);
            updateCarousel();
        });

        document.getElementById(nextId).addEventListener('click', () => {
            index = Math.min(boxes.length - 1, index + 1);
            updateCarousel();
        });

        updateCarousel();
    }

    // Initialize carousels
    setupCarousel('newArrivalsCarousel', 'newArrivalsIndicators', 'prevNewArrivals', 'nextNewArrivals');
    setupCarousel('bestSellersCarousel', 'bestSellersIndicators', 'prevBestSellers', 'nextBestSellers');
    setupCarousel('healthEssentialsCarousel', 'healthEssentialsIndicators', 'prevHealthEssentials', 'nextHealthEssentials');
</script>

</body>
</html>