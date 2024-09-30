<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect_to=cart.php');
    exit();
}

// Handle removing a book from the cart
if (isset($_GET['remove'])) {
    $book_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$book_id]);
    header('Location: cart.php');
    exit();
}

// Handle adding a book to the wishlist and removing it from the cart
if (isset($_GET['add_to_wishlist'])) {
    $book_id = intval($_GET['add_to_wishlist']);
    if (!isset($_SESSION['wishlist'][$book_id])) {
        $_SESSION['wishlist'][$book_id] = $book_id;
    }
    unset($_SESSION['cart'][$book_id]); // Remove the book from the cart
    header('Location: cart.php');
    exit();
}

// Initialize the total price
$total_price = 0.0;

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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
    <title>Cart - Priceless Pages</title>
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
        .cart-badge {
            position: relative;
        }
        .cart-badge .badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
        }
        .checkout-button {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            margin-bottom: 40px; /* Add margin to separate from footer */
        }
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>   

    <div class="container">
        <h2 class="mt-4">Your Cart</h2>
        <div class="row">
            <?php if (empty($_SESSION['cart'])): ?>
                <p class="col-12">Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $book_id => $item): ?>
                    <?php
                    // Fetch book details
                    $query = "SELECT id, title, price, image FROM books WHERE id = ?";
                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param('i', $book_id);
                        $stmt->execute();
                        $stmt->bind_result($id, $title, $price, $image);
                        $stmt->fetch();
                        $stmt->close();

                        $quantity = intval($item['quantity']);
                        $item_total = floatval($price) * $quantity;
                        $total_price += $item_total;
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="images/<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($title); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($title); ?></h5>
                                <p class="card-text">
                                    Unit Price: $<?php echo number_format($price, 2); ?><br>
                                    Quantity: <?php echo $quantity; ?><br>
                                    Total: $<?php echo number_format($item_total, 2); ?>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="cart.php?add_to_wishlist=<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm">❤️ Wishlist</a>
                                    <a href="cart.php?remove=<?php echo $id; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php endforeach; ?>
                <div class="col-12 checkout-button">
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
