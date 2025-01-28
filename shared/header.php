<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <style>
        /* General Styles */
        body, ul, li, a, button {
            margin: 0;
            padding: 0;
            list-style: none;
            text-decoration: none;
            box-sizing: border-box;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            color: white;
            padding: 0.5rem 1rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .search-bar {
            display: none;
            position: absolute;
            top: 60px;
            right: 10px;
            width: 80%;
            max-width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .dropdown {
            position: relative;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            color: black;
            min-width: 150px;
            border: 1px solid #ccc;
            border-radius: 4px;
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: black;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                background-color: #007BFF;
                position: absolute;
                top: 60px;
                right: 0;
                width: 100%;
            }

            .nav-links a {
                padding: 0.5rem 1rem;
                border-bottom: 1px solid #ccc;
            }

            .nav-links a:last-child {
                border-bottom: none;
            }

            .hamburger {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <header>
        <!-- Logo -->
        <div class="logo">Site Logo</div>

        <!-- Navigation Links -->
        <nav>
            <div class="hamburger" onclick="toggleMenu()">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <ul class="nav-links" id="nav-links">
                <?php
                // Example dynamic links for user/admin
                $userType = 'user'; // Change to 'admin' for admin links
                if ($userType === 'user') {
                    echo '<li><a href="#">Home</a></li>';
                    echo '<li><a href="#">Shop</a></li>';
                    echo '<li><a href="#">About Us</a></li>';
                    echo '<li><a href="#">Contact</a></li>';
                } elseif ($userType === 'admin') {
                    echo '<li><a href="#">Dashboard</a></li>';
                    echo '<li><a href="#">Users</a></li>';
                    echo '<li><a href="#">Products</a></li>';
                    echo '<li><a href="#">Orders</a></li>';
                    echo '<li><a href="#">Reports</a></li>';
                }
                ?>
            </ul>
        </nav>

        <!-- Icons -->
        <div class="icons">
            <button onclick="toggleSearch()">🔍</button>
            <div class="dropdown" onclick="toggleDropdown()">
                <span>👤</span>
                <div class="dropdown-menu" id="dropdown-menu">
                    <?php
                    if ($userType === 'user') {
                        echo '<a href="#">Profile</a>';
                        echo '<a href="#">Orders</a>';
                        echo '<a href="#">Logout</a>';
                    } elseif ($userType === 'admin') {
                        echo '<a href="#">Profile</a>';
                        echo '<a href="#">Logout</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Bar -->
    <div class="search-bar" id="search-bar">
        <input type="text" placeholder="Search...">
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById('nav-links');
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        }

        function toggleSearch() {
            const searchBar = document.getElementById('search-bar');
            searchBar.style.display = searchBar.style.display === 'block' ? 'none' : 'block';
        }

        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown if clicked outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('dropdown-menu');
            if (!event.target.closest('.dropdown')) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
