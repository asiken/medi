<?php
// Include the reusable header and footer
include_once('../../shared/header.php');
include_once('../../shared/footer.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Your Store</title>
    <!-- Include external styles and libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Add your custom CSS here -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .hero-section {
            position: relative;
            height: 100vh;
            background: url('hero-image.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }
        .hero-content h1 {
            font-size: 3rem;
            margin: 0 0 1rem;
        }
        .hero-content p {
            font-size: 1.5rem;
            margin: 0 0 2rem;
        }
        .hero-content .btn {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .hero-content .btn:hover {
            background-color: #0056b3;
        }
        .section-heading {
            padding: 1rem;
            background: #007BFF;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
        }
        .categories-section, .product-carousel-section, .parallax-section {
            padding: 1rem;
        }
        .category-card {
            display: inline-block;
            width: 100%;
            max-width: 200px;
            margin: 0.5rem;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .category-card img {
            width: 100%;
            height: auto;
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
        .parallax-section {
            position: relative;
            height: 300px;
            background: url('parallax-image.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .parallax-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .parallax-content {
            position: relative;
            color: white;
            z-index: 2;
        }
        .parallax-content h2 {
            font-size: 2rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to Your Store - Your Health, Our Priority</h1>
            <p>Trusted Healthcare Products Delivered to Your Doorstep</p>
            <a href="products.php" class="btn">Shop Now</a>
        </div>
    </section>

    <!-- Featured Categories Section -->
    <section class="categories-section">
        <div class="section-heading">Featured Categories</div>
        <div class="categories">
            <!-- Category Cards -->
            <div class="category-card">
                <img src="category1.jpg" alt="Category 1">
                <p>Category 1</p>
            </div>
            <div class="category-card">
                <img src="category2.jpg" alt="Category 2">
                <p>Category 2</p>
            </div>
            <!-- Add more categories dynamically -->
        </div>
    </section>

    <!-- Product Carousel Section -->
    <section class="product-carousel-section">
        <div class="section-heading">Featured Products</div>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- Example product slides -->
                <div class="swiper-slide">
                    <img src="product1.jpg" alt="Product 1">
                    <p>Product 1 - $10</p>
                    <button class="btn">Add to Cart</button>
                </div>
                <div class="swiper-slide">
                    <img src="product2.jpg" alt="Product 2">
                    <p>Product 2 - $15</p>
                    <button class="btn">Add to Cart</button>
                </div>
                <!-- Add more product slides dynamically -->
            </div>
            <!-- Navigation Arrows -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Parallax Section -->
    <section class="parallax-section">
        <div class="parallax-overlay"></div>
        <div class="parallax-content">
            <h2>Exclusive Offers Awaiting You</h2>
        </div>
    </section>

    <!-- Include Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper for the product carousel
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 5000,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>
</html>
