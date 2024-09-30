<?php
// Initialize cart session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Priceless Pages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: fantasy;
        }

        body {
            background: #f4f4f4;
        }

        .navbar {
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            background-color: #fff; /* Change this if you want a different navbar background color */
        }

        .navbar-brand img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        .navbar .nav-link {
            position: relative;
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 0.5em;
            text-transform: lowercase;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover {
            color: #007bff;
        }

        .navbar .nav-link:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 0%;
            background: #007bff;
            border-radius: 12px;
            transition: width 0.4s ease;
        }

        .navbar .nav-link:hover:before {
            width: 100%;
        }

        .navbar .nav-item.dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu .dropdown-item {
            font-size: 1.5rem;
            margin-bottom: 0.5em;
            text-transform: lowercase;
            padding: 0.5rem 1rem;
        }

        .dropdown-menu .dropdown-item:hover {
            color: #007bff;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="index.php">
        <img src="images/logo.png" alt="Priceless Pages">
        Priceless Pages
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="genresDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Genres
                </a>
                <div class="dropdown-menu" aria-labelledby="genresDropdown">
                    <a class="dropdown-item" href="genre.php?id=1">Best-Sellers</a>
                    <a class="dropdown-item" href="genre.php?id=3">Classics</a>
                    <a class="dropdown-item" href="genre.php?id=2">Our Selection</a>
                </div>
            </li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php">Cart <span class="badge badge-secondary"><?php echo count($_SESSION['cart']); ?></span></a></li>
            <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Account
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="dropdown-item" href="login.php?redirect_to=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">Login</a>
                        <a class="dropdown-item" href="register.php?redirect_to=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">Register</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
