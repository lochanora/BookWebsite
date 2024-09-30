<?php
include('config.php');
session_start();

// Get the book ID from the query parameter
$book_id = $_GET['id'];

// Fetch book details from the database using the book ID
$book_query = $conn->prepare("SELECT * FROM books WHERE id = ?");
$book_query->bind_param("i", $book_id);
$book_query->execute();
$book = $book_query->get_result()->fetch_assoc();

// Add to cart logic
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in, display message
        $_SESSION['message'] = "You need to login or create an account to add a book to your cart. <a href='login.php'>Login</a> or <a href='register.php'>Register</a> here";
        header("Location: book.php?id=$book_id");
        exit();
    }

    // Initialize the cart if it doesn't exist
    $cart = $_SESSION['cart'] ?? [];
    // Add or update the book quantity in the cart
    if (isset($cart[$book_id])) {
        $cart[$book_id]['quantity'] += 1; // Increment quantity if book already exists
    } else {
        $cart[$book_id] = ['quantity' => 1, 'details' => $book];
    }
    $_SESSION['cart'] = $cart;
    $_SESSION['cart_count'] = array_sum(array_column($cart, 'quantity'));
    header("Location: book.php?id=$book_id&added_to_cart=1");
    exit();
}

// Add to wishlist logic
if (isset($_POST['toggle_wishlist'])) {
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in, display message
        $_SESSION['message'] = "You need to login or create an account to add a book to your wishlist. <a href='login.php'>Login</a> or <a href='register.php'>Register</a>";
        header("Location: book.php?id=$book_id");
        exit();
    }

    // Initialize the wishlist if it doesn't exist
    $wishlist = $_SESSION['wishlist'] ?? [];
    // Check if the book is already in the wishlist
    if (isset($wishlist[$book_id])) {
        unset($wishlist[$book_id]); // Remove from wishlist if it exists
    } else {
        $wishlist[$book_id] = $book_id; // Add book ID to the wishlist
    }
    $_SESSION['wishlist'] = $wishlist;
    header("Location: book.php?id=$book_id");
    exit();
}

// Check if the book is in the wishlist
$is_in_wishlist = isset($_SESSION['wishlist'][$book_id]);

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
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <div class="container">
            <div class="book-detail row">
                <div class="col-md-4">
                    <img src="images/<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="img-fluid">
                </div>
                <div class="col-md-8">
                    <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                    <p><?php echo htmlspecialchars($book['description']); ?></p>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars($book['price']); ?></p>
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-warning">
                            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" class="mt-3">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                        <button type="submit" name="toggle_wishlist" class="btn btn-outline-danger"><?php echo $is_in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="recommendations container mt-5">
            <h2>Users also liked</h2>
            <div class="row">
                <?php
                // Fetch similar books (dummy data for example)
                $similar_books_stmt = $conn->prepare("SELECT * FROM books WHERE genre_id = ? AND id != ? LIMIT 4");
                $similar_books_stmt->bind_param("ii", $book['genre_id'], $book_id);
                $similar_books_stmt->execute();
                $similar_books_result = $similar_books_stmt->get_result();
                while ($similar_book = $similar_books_result->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <a href="book.php?id=<?php echo $similar_book['id']; ?>">
                                <img src="images/<?php echo htmlspecialchars($similar_book['image']); ?>" alt="<?php echo htmlspecialchars($similar_book['title']); ?>" class="card-img-top">
                            </a>
                            <div class="card-body">
                                <a href="book.php?id=<?php echo $similar_book['id']; ?>">
                                    <h5 class="card-title"><?php echo htmlspecialchars($similar_book['title']); ?></h5>
                                </a>
                            </div>
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
