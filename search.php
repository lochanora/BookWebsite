<?php
include('config.php');
session_start();

$query = htmlspecialchars($_GET['query'] ?? ''); // Sanitize input

// Prepare and bind parameters to prevent SQL injection
$search_results = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
$search_query = "%$query%";
$search_results->bind_param("ss", $search_query, $search_query);
$search_results->execute();
$results = $search_results->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <title>Search Results - Priceless Pages</title>
    <style>
        footer {
            display: block;
            text-align: left;
            background-color: black;
            color: white;
            padding: 20px;
            width: 100%;
            position: relative;
            bottom: 0;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-content .left-content {
            text-align: left;
        }

        .footer-content .right-content {
            text-align: right;
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .no-results {
            text-align: center;
            font-size: 1.2em;
            margin-top: 20px;
        }

        .book-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px;
        }

        .book {
            flex: 0 1 calc(25% - 20px);
            margin: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .book:hover {
            transform: translateY(-5px);
        }

        .book img {
            width: 100%;
            height: auto;
        }

        .book h3 {
            text-align: center;
            font-size: 1.1em;
            margin: 10px 0;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Search for books, authors...">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </header>
    <main>
        <?php if ($results->num_rows > 0): ?>
            <div class="book-list">
                <?php while ($book = $results->fetch_assoc()): ?>
                    <div class="card book">
                        <a href="book.php?id=<?php echo $book['id']; ?>">
                            <img src="images/<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No results found for "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </main>
<footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
