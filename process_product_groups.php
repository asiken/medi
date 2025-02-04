<?php
include 'config.php'; // Your database connection file

if (isset($_POST['update_groups'])) {
    $product_id = $_POST['product_id'];
    $groups = isset($_POST['groups']) ? $_POST['groups'] : [];

    // First, clear existing group assignments for this product
    $delete_existing = $conn->prepare("DELETE FROM product_groups WHERE product_id = ?");
    $delete_existing->execute([$product_id]);

    // Then, insert the new group assignments
    foreach ($groups as $group_name) {
        $insert_group = $conn->prepare("INSERT INTO product_groups (product_id, group_name) VALUES (?, ?)");
        $insert_group->execute([$product_id, $group_name]);
    }

    header('location: admin_products.php'); // Redirect back to the products page
}
?>