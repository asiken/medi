<?php
@include 'config.php';

$select_conditions = $conn->prepare("SELECT id, condition_name FROM conditions");
$select_conditions->execute();

while ($condition = $select_conditions->fetch(PDO::FETCH_ASSOC)) {
   echo "<div class='subcat' onclick='showProductsByCondition({$condition['id']})'>{$condition['condition_name']}</div>";
}
?>
