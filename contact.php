<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['send'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);

    $select_message = $conn->prepare("SELECT * FROM `message` WHERE name = ? AND email = ? AND number = ? AND message = ?");
    $select_message->execute([$name, $email, $number, $msg]);

    if ($select_message->rowCount() > 0) {
        $message[] = 'Message already sent!';
    } else {
        $insert_message = $conn->prepare("INSERT INTO `message`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
        $insert_message->execute([$user_id, $name, $email, $number, $msg]);
        $message[] = 'Message sent successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS File -->
    <link rel="stylesheet" href="css/style.css">

    <style>
       
            body {
   background-image: url('images/6bg.png') !important;
   background-size: cover;
   background-position: center;
   background-attachment: fixed;
   background-repeat: no-repeat;
   font-family: 'Rubik', sans-serif;
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   color: var(--black);
   position: relative; /* Add position relative for overlay positioning */
}

/* Dark overlay for content readability */
body::before {
   content: '';
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
   z-index: -1; /* Place overlay behind content */
}
        .contact {
            text-align: center;
            padding: 50px 20px;
        }
        .social-icons {
            text-align: center;
            margin-top: 20px;
        }
        .social-icons a {
            display: inline-block;
            margin: 10px;
            font-size: 24px;
            color: #fff;
            width: 50px;
            height: 50px;
            line-height: 50px;
            border-radius: 50%;
            text-align: center;
            background: #007bff;
            transition: 0.3s;
        }
        .social-icons a:hover {
            background: #0056b3;
        }
        .box::placeholder {
            color: white;
        }
    </style>
</head>
<body>
    
<?php include 'header.php'; ?>

<section class="contact">
    <h1 class="title">Get in Touch</h1>
    <form action="" method="POST">
        <input type="text" name="name" class="box" required placeholder="Enter your name">
        <input type="email" name="email" class="box" required placeholder="Enter your email">
        <input type="number" name="number" min="0" class="box" required placeholder="Enter your number">
        <textarea name="msg" class="box" required placeholder="Enter your message" cols="30" rows="10"></textarea>
        <input type="submit" value="Send Message" class="btn" name="send">
    </form>

    <div class="social-icons">
        <a href="https://www.facebook.com" target="_blank" class="fab fa-facebook-f"></a>
        <a href="https://twitter.com" target="_blank" class="fab fa-twitter"></a>
        <a href="https://www.instagram.com" target="_blank" class="fab fa-instagram"></a>
        <a href="https://www.linkedin.com" target="_blank" class="fab fa-linkedin"></a>
        <a href="https://wa.me/0721884954" target="_blank" class="fab fa-whatsapp" style="background: #25D366;"></a>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
