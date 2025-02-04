<?php

@include 'config.php';
use Twilio\Rest\Client;  // Move the use statement here

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

// Fetch user profile details
$profile_query = $conn->prepare("SELECT name FROM `users` WHERE id = ?");
$profile_query->execute([$user_id]);
$fetch_profile = $profile_query->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['order'])) {
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $flat = $_POST['flat'];
    $flat = filter_var($flat, FILTER_SANITIZE_STRING);
    $street = $_POST['street'];
    $street = filter_var($street, FILTER_SANITIZE_STRING);
    $method = $_POST['method'];
    $method = filter_var($method, FILTER_SANITIZE_STRING);
    $placed_on = date('d-M-Y');

    $address = $flat . ', ' . $street; // Combine address lines
    $cart_total = 0;
    $cart_products = [];

    // Fetch cart items for the current user
    $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_query->execute([$user_id]);

    if ($cart_query->rowCount() > 0) {
        while ($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
            $cart_products[] = $cart_item['name'] . ' ( ' . $cart_item['quantity'] . ' )';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    // Check for duplicate orders
    $order_query = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? AND number = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
    $order_query->execute([$user_id, $number, $method, $address, $total_products, $cart_total]);

    if ($cart_total == 0) {
        $message[] = 'Your cart is empty.';
    } elseif ($order_query->rowCount() > 0) {
        $message[] = 'Order placed already!';
    } else {
        // Insert new order
        $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_order->execute([$user_id, $fetch_profile['name'], $number, $method, $address, $total_products, $cart_total, $placed_on]);

        // Clear the user's cart
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        // Twilio API integration
        require_once 'vendor/autoload.php'; // Update with the correct path to Twilio's autoload.php

        $sid    = 'ACf5a939213be0c7e80254fbba69e5e3b4';
        $token  = 'a42eb104271b1cb8854009adec9f2105';
        $twilio = new Client($sid, $token);

        $message_body = "New Order Received:\n";
        $message_body .= "Name: {$fetch_profile['name']}\n";
        $message_body .= "Number: $number\n";
        $message_body .= "Address: $address\n";
        $message_body .= "Products: $total_products\n";
        $message_body .= "Total: $$cart_total\n";
        $message_body .= "Payment Method: $method\n";
        $message_body .= "Placed On: $placed_on";

        try {
         $response = $twilio->messages->create(
             'whatsapp:+254100354117', // Admin's WhatsApp number
             [
                 'from' => 'whatsapp:+14155238886', // Twilio's sandbox number
                 'body' => $message_body
             ]
         );
     
         // You can access response data like this:
         $message[] = 'Order placed successfully! WhatsApp notification sent. Message SID: ' . $response->sid;
     } catch (Exception $e) {
         $message[] = 'Order placed, but failed to send WhatsApp notification: ' . $e->getMessage();
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
   <title>Checkout</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="css/style.css">



   <style>
      body {
   background-image: url('images/13bg.png') !important;
   background-size: cover;
   background-position: center;
   background-attachment: fixed;
   background-repeat: no-repeat;
   font-family: 'Rubik', sans-serif;
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   color: var(--black);
}
   </style>


</head>



<body>

<?php include 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if ($select_cart_items->rowCount() > 0) {
         while ($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)) {
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p><?= $fetch_cart_items['name']; ?> <span>(<?= 'Ksh '.$fetch_cart_items['price'].'/- x '.$fetch_cart_items['quantity']; ?>)</span></p>
   <?php
         }
      } else {
         echo '<p class="empty">Your cart is empty!</p>';
      }
   ?>
   <div class="grand-total">Grand Total: <span>Ksh <?= $cart_grand_total; ?>/-</span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>Place Your Order</h3>

      <div class="flex">
        
         <div class="inputBox">
            <span>Your Number:</span>
            <input type="number" name="number" placeholder="Enter your number" class="box" required>
         </div>
         <div class="inputBox">
            <span>Flat:</span>
            <input type="text" name="flat" placeholder=" Flat Name/Number" class="box" required>
         </div>
         <div class="inputBox">
            <span>Street/Road:</span>
            <input type="text" name="street" placeholder="Street Name" class="box" required>
         </div>
         <div class="inputBox">
            <span>Payment Method:</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Cash on Delivery</option>
               <option value="mpesa">MPESA</option>
            </select>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1) ? '' : 'disabled'; ?>" value="Place Order">

   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>

</html>
