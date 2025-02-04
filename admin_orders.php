<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

// Twilio API Credentials
$account_sid = 'ACf5a939213be0c7e80254fbba69e5e3b4';
$auth_token = 'a42eb104271b1cb8854009adec9f2105';
$twilio_number = 'whatsapp:+14155238886';
$admin_number = 'whatsapp:+254100354117';

// Handle order update and notification
if (isset($_POST['update_order'])) {
   $order_id = $_POST['order_id'];
   $update_payment = filter_var($_POST['update_payment'], FILTER_SANITIZE_STRING);

   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_orders->execute([$update_payment, $order_id]);

   $fetch_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
   $fetch_order->execute([$order_id]);
   $order_details = $fetch_order->fetch(PDO::FETCH_ASSOC);

   $whatsapp_message = "Order Updated:\n";
   $whatsapp_message .= "Name: {$order_details['name']}\n";
   $whatsapp_message .= "Phone: {$order_details['number']}\n";
   $whatsapp_message .= "Address: {$order_details['address']}\n";
   $whatsapp_message .= "Total Products: {$order_details['total_products']}\n";
   $whatsapp_message .= "Total Price: $ {$order_details['total_price']}\n";
   $whatsapp_message .= "Payment Status: {$update_payment}";

   $url = "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json";
   $data = [
       'From' => $twilio_number,
       'To' => $admin_number,
       'Body' => $whatsapp_message
   ];

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
   curl_setopt($ch, CURLOPT_USERPWD, "$account_sid:$auth_token");
   $response = curl_exec($ch);
   curl_close($ch);

   $message[] = 'Payment updated! WhatsApp notification sent.';
}

// Handle order deletion
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');
}

// Pagination, search, and filter
$items_per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';

$query = "SELECT * FROM `orders` WHERE 1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR address LIKE ? OR number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_status)) {
    $query .= " AND payment_status = ?";
    $params[] = $filter_status;
}

// Date Filter
if (!empty($date_range)) {
    $dates = explode(" - ", $date_range);
    if (count($dates) == 2) {
        $start_date = $dates[0] . " 00:00:00";
        $end_date = $dates[1] . " 23:59:59";

        $query .= " AND placed_on BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }
}

$query .= " ORDER BY placed_on DESC LIMIT $items_per_page OFFSET $offset";

$select_orders = $conn->prepare($query);
$select_orders->execute($params);

// Count Total Orders with Filters
$count_query = "SELECT COUNT(*) FROM `orders` WHERE 1";
$count_params = [];

if (!empty($search)) {
    $count_query .= " AND (name LIKE ? OR address LIKE ? OR number LIKE ?)";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

if (!empty($filter_status)) {
    $count_query .= " AND payment_status = ?";
    $count_params[] = $filter_status;
}

if (!empty($date_range)) {
    if (count($dates) == 2) {
        $count_query .= " AND placed_on BETWEEN ? AND ?";
        $count_params[] = $start_date;
        $count_params[] = $end_date;
    }
}

$total_orders_stmt = $conn->prepare($count_query);
$total_orders_stmt->execute($count_params);
$total_orders = $total_orders_stmt->fetchColumn();
$total_pages = ceil($total_orders / $items_per_page);





?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   
   <style>
    body {
        background-image: url('images/2bg.png') !important;
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

    .search-filter-form {
        display: flex;
        align-items: center; /* Vertically align items */
        gap: 10px; /* Space between elements */
        margin-bottom: 20px; /* Space below the form */
        flex-wrap: wrap; /* Allow form elements to wrap on smaller screens */
    }

    .search-filter-form input[type="text"],
    .search-filter-form select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        flex: 1; /* Allow inputs to expand */
        min-width: 150px; /* Set a minimum width */
    }

    .search-filter-form button {
        padding: 10px 15px;
        background-color: #007bff; /* Example color */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        flex: 0; /* Don't let button expand */
    }

    .search-filter-form button:hover {
        background-color: #0056b3; /* Darker shade on hover */
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .pagination a {
        padding: 10px 15px;
        margin: 0 5px;
        border: 1px solid #ddd;
        text-decoration: none;
        color: #000;
        border-radius: 5px; /* Rounded corners for pagination links */
    }

    .pagination a.active {
        background-color: #333;
        color: #fff;
    }

    /* Style for the order boxes (adjust as needed) */
    .box-container .box {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
    }

    .box p {
        margin-bottom: 10px;
    }

    .box span {
        font-weight: bold;
    }

    .drop-down {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    .daterangepicker {
        z-index: 9999 !important;
    }

    /* Mobile-friendly adjustments */
    @media screen and (max-width: 768px) {
        .search-filter-form {
            flex-direction: column; /* Stack elements vertically on smaller screens */
            align-items: flex-start; /* Align elements to the left */
        }

        .search-filter-form input[type="text"],
        .search-filter-form select,
        .search-filter-form button {
            width: 100%; /* Make all form elements take full width */
            margin-bottom: 10px; /* Add spacing between elements */
        }

        .pagination {
            flex-direction: column; /* Stack pagination links vertically on smaller screens */
            align-items: center; /* Center pagination links */
        }

        .box-container .box {
            padding: 15px; /* Adjust padding to fit mobile view */
        }
    }

    /* For very small screens */
    @media screen and (max-width: 480px) {
        .search-filter-form input[type="text"],
        .search-filter-form select,
        .search-filter-form button {
            font-size: 14px; /* Reduce font size for smaller screens */
        }

        .box-container .box {
            padding: 10px; /* Reduce padding for smaller screens */
        }
    }
</style>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

  
   <form method="GET" class="search-filter-form">
        <input type="text" name="search" placeholder="Search orders" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <input type="text" id="dateRange" name="date_range" placeholder="Select Date Range" value="<?= htmlspecialchars($date_range) ?>">
        <button type="submit">Apply</button>
    </form>

    <div class="box-container">
    <?php
    if ($select_orders->rowCount() > 0) {
        while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box">
                <p> User ID: <span><?= htmlspecialchars($fetch_orders['user_id']); ?></span> </p>
                <p> Placed On: <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span> </p>
                <p> Name: <span><?= htmlspecialchars($fetch_orders['name']); ?></span> </p>
                <p> Number: <span><?= htmlspecialchars($fetch_orders['number']); ?></span> </p>
                <p> Address: <span><?= htmlspecialchars($fetch_orders['address']); ?></span> </p>
                <p> Total Products: <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span> </p>
                <p> Total Price: <span>$<?= htmlspecialchars($fetch_orders['total_price']); ?>/-</span> </p>
                <p> Payment Method: <span><?= htmlspecialchars($fetch_orders['method']); ?></span> </p>
                <form action="" method="POST">
                    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                    <select name="update_payment" class="drop-down">
                        <option value="<?= $fetch_orders['payment_status']; ?>" selected><?= $fetch_orders['payment_status']; ?></option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="flex-btn">
                        <input type="submit" name="update_order" class="option-btn" value="Update">
                        <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">Delete</a>
                    </div>
                </form>
            </div>
            <?php
        }
    } else {
        echo '<p class="empty" style="text-align: center; font-size: 18px; color: red;">No orders found for the selected criteria!</p>';
    }
    ?>
</div>


    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($filter_status) ?>&date=<?= isset($_GET['date']) ? $_GET['date'] : ''; ?>" class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php } ?>
    </div>

</section>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (required for Date Range Picker) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="js/script.js"></script>
<script>
$(document).ready(function() {
    // Ensure the date range picker is applied
    $('#dateRange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        }
    });

    // When user applies a date range, update input and submit the form
    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        
        // Auto-submit the form
        $('.search-filter-form').submit();
    });

    // When user cancels selection, clear input and submit the form
    $('#dateRange').on('cancel.daterangepicker', function() {
        $(this).val('');

        // Auto-submit the form
        $('.search-filter-form').submit();
    });
});


</script>

</body>
</html>