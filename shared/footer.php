<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        footer {
            background-color: white;
            border-top: 1px solid lightgray;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto; /* Ensures footer sticks to bottom */
            font-size: 0.9rem;
        }

        footer .links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 0.5rem 0;
        }

        footer .links a {
            text-decoration: none;
            color: #007BFF;
            font-weight: 500;
        }

        footer .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        footer .social-icons span {
            display: inline-block;
            width: 25px;
            height: 25px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            line-height: 25px;
            border-radius: 50%;
            font-size: 0.9rem;
            cursor: pointer;
        }

        footer .social-icons span:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <footer>
        <!-- Copyright -->
        <div>&copy; <?php echo date('Y'); ?> KenimoKenya. All Rights Reserved.</div>

        <!-- Links -->
        <div class="links">
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Contact Us</a>
        </div>

        <!-- Social Media Icons -->
        <div class="social-icons">
            <!-- Placeholder for icons -->
            <span>F</span> <!-- Facebook -->
            <span>T</span> <!-- Twitter -->
            <span>I</span> <!-- Instagram -->
            <span>L</span> <!-- LinkedIn -->
        </div>
    </footer>
</body>
</html>
