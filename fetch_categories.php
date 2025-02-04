<?php
@include 'config.php';

$select_categories = $conn->prepare("SELECT id, category_name FROM categories");
$select_categories->execute();

while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
   echo "<div class='subcat' onclick='showSubcategories({$category['id']})'>{$category['category_name']}</div>";
}
?>
