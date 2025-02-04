<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

$items_per_page = 20; // Items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Handle search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT * FROM orders WHERE user_id = ?";
$params = [$user_id];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR address LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_status)) {
    $query .= " AND payment_status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY placed_on DESC LIMIT $items_per_page OFFSET $offset";

$select_orders = $conn->prepare($query);
$select_orders->execute($params);

// Count total orders for pagination
$count_query = "SELECT COUNT(*) FROM orders WHERE user_id = ?";
$count_params = [$user_id];

if (!empty($search)) {
    $count_query .= " AND (name LIKE ? OR address LIKE ?)";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

if (!empty($filter_status)) {
    $count_query .= " AND payment_status = ?";
    $count_params[] = $filter_status;
}

$total_orders_stmt = $conn->prepare($count_query);
$total_orders_stmt->execute($count_params);
$total_orders = $total_orders_stmt->fetchColumn();
$total_pages = ceil($total_orders / $items_per_page);



// Date filter handling
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

if (!empty($filter_date_from) && !empty($filter_date_to)) {
    $query .= " AND placed_on BETWEEN ? AND ?";
    $params[] = $filter_date_from;
    $params[] = $filter_date_to;

    $count_query .= " AND placed_on BETWEEN ? AND ?";
    $count_params[] = $filter_date_from;
    $count_params[] = $filter_date_to;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      body {
         background-image: url('images/5bg.png') !important;
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
      .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: rgba(0, 0, 0, 0.7); /* Darker pagination background */
            border: none;
            color: #eee;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a.active,
        .pagination a:hover {
            background-color: #00ffff; /* Cyan pagination on hover and active */
            color: #333;
        }
        .search-filter-form {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent dark background for form */
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex; /* Use flexbox for layout */
            flex-wrap: wrap; /* Allow items to wrap */
            gap: 10px; /* Spacing between form elements */
            justify-content: center;
        }

        .search-filter-form input,
        .search-filter-form select,
        .search-filter-form button {
            background-color: rgba(255, 255, 255, 0.1); /* Very light, almost transparent background */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle white border */
            padding: 10px;
            border-radius: 5px;
            color: #eee;
            transition: background-color 0.3s, border-color 0.3s; /* Smooth transitions */
        }

        .search-filter-form input:focus,
        .search-filter-form select:focus {
            background-color: rgba(255, 255, 255, 0.2); /* Slightly darker background on focus */
            border-color: rgba(255, 255, 255, 0.5); /* More visible border on focus */
            outline: none;
        }

        .search-filter-form button {
            background-color: #00ffff; /* Cyan button */
            color: #333;
            cursor: pointer;
            border: none;
        }

        .search-filter-form button:hover {
            background-color: #00aaff; /* Slightly darker cyan on hover */
        }

        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive grid */
            gap: 20px;
        }

        .box {
            background-color: rgba(0, 0, 0, 0.8); /* Darker box background */
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* More prominent shadow */
            padding: 20px; /* Increased padding */
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .box:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
        }

        .box p {
            margin: 8px 0; /* Increased margin */
        }

        .box p span {
            color: #00ffff; /* Cyan highlight color */
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less */
            max-width: 600px; /* set a maximum width */
            box-sizing: border-box;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Styles for the order details inside the modal */
        .modal-content p {
            margin: 5px 0;
        }

    </style>
 </head>
<body>

<?php include 'header.php'; ?>

<section class="placed-orders">

    <form method="GET" class="search-filter-form">  
        <input type="text" name="search" placeholder="Search orders" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>

        <label for="date_from">From:</label>
        <input type="date" name="date_from" id="date_from" value="<?= $filter_date_from ?>">
        <label for="date_to">To:</label>
        <input type="date" name="date_to" id="date_to" value="<?= $filter_date_to ?>">

        <button type="submit">Apply</button>
    </form>

    <div class="box-container">
        <?php
        if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box" data-order-id="<?= $fetch_orders['id'] ?>">
                    <p>Placed On: <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
                    <p>Name: <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
                    <p>Number: <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
                    <p>Payment Status: <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') { echo 'red'; } else { echo 'green'; }; ?>"><?= htmlspecialchars($fetch_orders['payment_status']); ?></span></p>
                </div>

                <div id="orderModal<?= $fetch_orders['id'] ?>" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal(<?= $fetch_orders['id'] ?>)">&times;</span>
                        <h2>Order Details</h2>
                        <p>Placed On: <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
                        <p>Name: <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
                        <p>Number: <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
                        <p>Address: <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
                        <p>Payment Method: <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
                        <p>Your Orders: <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
                        <p>Total Price: <span>Ksh <?= htmlspecialchars($fetch_orders['total_price']); ?>/-</span></p>
                        <p>Payment Status: <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') { echo 'red'; } else { echo 'green'; }; ?>"><?= htmlspecialchars($fetch_orders['payment_status']); ?></span></p>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No orders found!</p>';
        }
        ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($filter_status) ?>&date_from=<?= $filter_date_from ?>&date_to=<?= $filter_date_to ?>" class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php } ?>
    </div>
</section>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script>
    const boxes = document.querySelectorAll('.box');

    boxes.forEach(box => {
        box.addEventListener('click', () => {
            const orderId = box.dataset.orderId;
            const modal = document.getElementById(`orderModal${orderId}`);
            modal.style.display = 'block';
        });
    });

    function closeModal(orderId) {
        const modal = document.getElementById(`orderModal${orderId}`);
        modal.style.display = 'none';
    }

    // Close the modal if the user clicks outside of the modal content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.style.display = "none";
            });
        }
    }

</script>

</body>
</html>