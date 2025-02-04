<?php
@include 'config.php';

$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle search suggestions
if (isset($_GET['search']) && !isset($_GET['full_search'])) {
    $search = "%" . $_GET['search'] . "%";
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE name LIKE ? LIMIT 10");
    $stmt->execute([$search]);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
    exit;
}

// Build main query
$query = "SELECT id, name, price, image FROM products WHERE 1";
$params = [];

// Apply filters (IMPROVED - More robust logic)
if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
    $query .= " AND category_id = ?";
    $params[] = $_GET['category_id'];
}
if (isset($_GET['subcategory_id']) && $_GET['subcategory_id'] != '') {
    $query .= " AND subcategory_id = ?";
    $params[] = $_GET['subcategory_id'];
}
if (isset($_GET['condition_id']) && $_GET['condition_id'] != '') {
    $query .= " AND condition_id = ?";
    $params[] = $_GET['condition_id'];
}
if (isset($_GET['search']) && $_GET['search'] != '') {
    $query .= " AND name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

$query .= " LIMIT $start, $limit";
$select_products = $conn->prepare($query);
$select_products->execute($params);

// Generate product cards
if ($select_products->rowCount() > 0) {
    while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        echo "<form action='cart_wishlist.php' class='box' method='POST'>
                <img src='uploaded_img/{$product['image']}' alt=''>
                <div class='name'>{$product['name']}</div>
                <div class='price'>Ksh {$product['price']}</div>
                <input type='hidden' name='pid' value='{$product['id']}'>
                <input type='hidden' name='p_name' value='{$product['name']}'>
                <input type='hidden' name='p_price' value='{$product['price']}'>
                <input type='hidden' name='p_image' value='{$product['image']}'>
                <input type='number' min='1' value='1' name='p_qty' class='qty'>
                <button type='submit' class='option-btn' name='add_to_wishlist'>Add to Wishlist</button>
                <button type='submit' class='btn' name='add_to_cart'>Add to Cart</button>
              </form>";
    }
} else {
    echo "<p class='empty'>No products found!</p>";
}


// Pagination
$total_query = "SELECT COUNT(*) FROM products WHERE 1";
$total_params = [];
if (!empty($_GET['subcategory_id'])) {
    $total_query .= " AND subcategory_id = ?";
    $total_params[] = $_GET['subcategory_id'];
}
if (!empty($_GET['condition_id'])) {
    $total_query .= " AND condition_id = ?";
    $total_params[] = $_GET['condition_id'];
}
if (!empty($_GET['search'])) {
    $total_query .= " AND name LIKE ?";
    $total_params[] = "%" . $_GET['search'] . "%";
}

$total_results = $conn->prepare($total_query);
$total_results->execute($total_params);
$total_pages = ceil($total_results->fetchColumn() / $limit);

if ($total_pages > 1) {
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='?search=" . urlencode($_GET['search'] ?? '') . "&page=$i'" . ($i == $page ? " class='active'" : "") . ">$i</a> ";
    }
    echo "</div>";
}
?>
