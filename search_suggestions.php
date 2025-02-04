<?php
@include 'config.php';

if(isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = filter_var($query, FILTER_SANITIZE_STRING);
    // Fetch products that match the search query
    $stmt = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? LIMIT 5");
    $stmt->execute(['%' . $query . '%']);
    
    if($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="suggestion" onclick="selectSuggestion(\'' . $row['name'] . '\')">' . $row['name'] . '</div>';
        }
    } else {
        echo '<div class="no-suggestions">No suggestions found</div>';
    }
}
?>
