<?php
include 'config.php';

$categoryId = $_GET['category_id'];

$select_subcategories = $conn->prepare("SELECT id, subcategory_name FROM subcategories WHERE category_id = ?"); // Select id and Sname
$select_subcategories->execute([$categoryId]);
$subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($subcategories);
?>