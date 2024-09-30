
<?php
include('config.php');
session_start();

// Fetch genres
$genres = $conn->query("SELECT * FROM genres");

// Fetch 3 random books for the selection of the day
$selection_of_the_day = $conn->query("SELECT * FROM books ORDER BY RAND() LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Priceless Pages</title>
<style>
    /* General Reset */
*, *:before, *:after {
    box-sizing: border-box;
}

/* Set the body and html to fill the height of the viewport */
html, body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Ensure the main content area takes up the remaining space */
main {
    flex: 1;
}

/* Footer Styles */
.footer {
    background-color: #000; /* Dark background */
    color: #fff; /* White text */
    text-align: center;
    padding: 6px 0;
    width: 100%;
    position: relative;
    bottom: 0;
    margin-top: auto;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    max-width: 1100px;
    margin: 0 auto;
    padding: 10px;
    justify-content: space-between;
    text-align: left;
}

.footer__addr {
    flex: 1 1 300px;
    margin-right: 1.25em;
}

.footer__logo {
    font-family: 'Pacifico', cursive;
    font-size: 1.5rem;
    margin-bottom: 0.5em;
    text-transform: lowercase;
}

.footer__addr address {
    font-style: normal;
    color: #999;
}

.footer__btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50px;
    color: #fff;
    text-decoration: none;
    margin-top: 10px;
}

.footer__btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.footer__nav {
    display: flex;
    flex: 2 1 600px;
    justify-content: space-between;
    flex-wrap: wrap;
}

.nav__item {
    flex: 1 1 150px;
    margin-bottom: 1.5em;
}

.nav__title {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 0.5em;
    text-transform: uppercase;
}

.nav__ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav__ul a {
    color: #999;
    text-decoration: none;
}

.nav__ul a:hover {
    text-decoration: underline;
}

.legal {
    border-top: 1px solid #333;
    margin-top: 20px;
    padding-top: 10px;
    text-align: center;
    color: #999;
}

@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer_addr, .footernav, .nav_item {
        flex: 1 1 100%;
    }

    .footer__nav {
        margin-top: 20px;
    }

    .legal {
        margin-top: 20px;
    }
}

        .card-img-top {
            width: 90%;
            height: 180px;
            object-fit: cover;
        }
        .card {
            margin-bottom: 20px;
        }
        
.search-form {
    display: flex;
    justify-content: center;
    margin: 0 auto;
    padding: 20px 0;
    position: absolute;
    left: 50%;
    top: 28%; /* Adjusted from 30% to 28% */
    transform: translateX(-50%); /* Centers horizontally */
    z-index: 1;
}


.search-form input[type="text"] {
    width: 400px; /* Adjust the width as needed */
    padding: 10px 15px; /* Padding for the input */
    border-radius: 30px 0 0 30px; /* Rounded corners for the left side */
    border: 1px solid #ddd; /* Border styling */
    outline: none; /* Remove default outline */
}

.search-form button {
    padding: 10px 20px; /* Padding for the button */
    border-radius: 0 30px 30px 0; /* Rounded corners for the right side */
    border: none; /* Remove border */
    background-color: transparent; /* Make background transparent */
    color: white; /* Keep the icon white */
    cursor: pointer; /* Pointer cursor on hover */
    font-size: 18px; /* Size of the icon */
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-form button:hover {
    background-color: rgba(255, 255, 255, 0.2); /* Slightly visible on hover */
}

.search-form button:hover {
    background-color: #003cb3; /* Darker shade on hover */
}

.carousel-caption {
            position: absolute;
            top: 63%;
            left: 30%;
            transform: translate(-5%, -50%);
            text-align: center;
            color: white;
            z-index: 1;
        }
        

</style>
</head>
<body>
    <header>
    <?php include 'navbar.php'; ?>
</header>

<main>
    <!-- Place the search form inside the main content or hero section -->
    <section class="hero">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="images/books.jpeg" alt="First slide">
                    <div class="carousel-caption d-none d-md-block">
                        <h1>Welcome to Priceless Pages</h1>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/books3.jpeg" alt="Second slide">
                    <div class="carousel-caption d-none d-md-block">
                        <h1>Welcome to Priceless Pages</h1>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/books.jpg" alt="Third slide">
                    <div class="carousel-caption d-none d-md-block">
                        <h1>Welcome to Priceless Pages</h1>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <!-- Search Form Section -->
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Search for books, authors..." required>
            <button type="submit" class="fa fa-search"></button>
        </form>
    </section>
     <div class="container">
            <!-- Genres Section -->
            <h2 class="mt-4">Genres</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <a href="genre.php?id=1" class="card-link">Best-Selling</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <a href="genre.php?id=2" class="card-link">Our Selection</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <a href="genre.php?id=3" class="card-link">Classics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="selection-of-the-day container mt-4">
            <h2>Our Selection of the Day</h2>
            <div class="row">
                <?php while($book = $selection_of_the_day->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="book.php?id=<?php echo $book['id']; ?>">
                                <img src="images/<?php echo $book['image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="card-img-top">
                            </a>
                            <div class="card-body">
                                <a href="book.php?id=<?php echo $book['id']; ?>">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
</main>


    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
