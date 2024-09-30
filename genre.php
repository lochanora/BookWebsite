<?php
include('config.php');
session_start();

// Fetch genre id from URL
$genre_id = $_GET['id'];

// Fetch genre details
$genre_stmt = $conn->prepare("SELECT * FROM genres WHERE id = ?");
$genre_stmt->bind_param("i", $genre_id);
$genre_stmt->execute();
$genre_result = $genre_stmt->get_result();
$genre = $genre_result->fetch_assoc();

// Fetch books in this genre
$books_stmt = $conn->prepare("SELECT * FROM books WHERE genre_id = ?");
$books_stmt->bind_param("i", $genre_id);
$books_stmt->execute();
$books_result = $books_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title><?php echo htmlspecialchars($genre['name']); ?> - Priceless Pages</title>
    <style>
        body {
            padding-top: 20px;
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

        .book h5 {
            text-align: center;
            font-size: 1.1em;
            margin: 10px 0;
            color: #333;
        }

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
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <div class="container">
            <h1 class="text-center mb-4"><?php echo htmlspecialchars($genre['name']); ?></h1>
            <div class="book-list">
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <div class="card book">
                        <a href="book.php?id=<?php echo $book['id']; ?>">
                            <img src="images/<?php echo htmlspecialchars($book['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        </a>
                        <div class="card-body">
                            <a href="book.php?id=<?php echo $book['id']; ?>">
                                <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
