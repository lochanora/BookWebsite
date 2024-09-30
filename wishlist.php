<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect_to=wishlist.php');
    exit();
}

// Handle removing a book from the wishlist
if (isset($_GET['remove'])) {
    $book_id = intval($_GET['remove']);
    if (isset($_SESSION['wishlist'][$book_id])) {
        unset($_SESSION['wishlist'][$book_id]);
    }
    header('Location: wishlist.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Wishlist - Priceless Pages</title>
    <style>
        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>

    <div class="container">
        <h2 class="mt-4">Your Wishlist</h2>
        <div class="row">
            <?php if (empty($_SESSION['wishlist'])): ?>
                <p class="col-12">Your wishlist is empty.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['wishlist'] as $book_id): ?>
                    <?php
                    // Fetch book details
                    $query = "SELECT id, title, price, image FROM books WHERE id = ?";
                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param('i', $book_id);
                        $stmt->execute();
                        $stmt->bind_result($id, $title, $price, $image);
                        $stmt->fetch();
                        $stmt->close();
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="images/<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($title); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($title); ?></h5>
                                <p class="card-text">$<?php echo number_format($price, 2); ?></p>
                                <a href="wishlist.php?remove=<?php echo $id; ?>" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
